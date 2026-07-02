from flask import Flask, request, jsonify
from docx import Document
from flask_cors import CORS  # <-- import CORS

app = Flask(__name__)
CORS(app)
import joblib, os
import PyPDF2
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

try:
    from sentence_transformers import SentenceTransformer, util
except Exception:
    SentenceTransformer = None
    util = None


# Load pretrained models
clf = joblib.load("resume_classifier.pkl")
embed_model = SentenceTransformer('all-MiniLM-L6-v2') if SentenceTransformer else None


# =========================
# Helpers
# =========================
def read_docx(file_path):
    doc = Document(file_path)
    return "\n".join([p.text.strip() for p in doc.paragraphs if p.text.strip()])


def read_pdf(file_path):
    text = ""
    with open(file_path, 'rb') as f:
        reader = PyPDF2.PdfReader(f)
        for page in reader.pages:
            text += page.extract_text() + "\n"
    return text.strip()


def read_cv(file_path):
    ext = file_path.split('.')[-1].lower()
    if ext == 'docx':
        return read_docx(file_path)
    elif ext == 'pdf':
        return read_pdf(file_path)
    else:
        raise ValueError("Unsupported file format. Only DOCX or PDF allowed.")


def compute_similarity(job_text, cv_text):
    if embed_model is None or util is None:
        vectorizer = TfidfVectorizer(stop_words='english')
        vectors = vectorizer.fit_transform([job_text, cv_text])
        return float(cosine_similarity(vectors[0], vectors[1]).item())

    job_emb = embed_model.encode(job_text, convert_to_tensor=True, normalize_embeddings=True)
    cv_emb = embed_model.encode(cv_text, convert_to_tensor=True, normalize_embeddings=True)
    return float(util.cos_sim(job_emb, cv_emb).item())


# =========================
# API Endpoint
# =========================
@app.route('/match-score', methods=['POST'])
def match_score():
    # Get job description text
    job_text = request.form.get('job_text')
    if not job_text:
        return jsonify({"error": "Job description text is required"}), 400

    # Get CV file
    if 'cv_file' not in request.files:
        return jsonify({"error": "CV file is required"}), 400

    cv_file = request.files['cv_file']
    temp_path = "temp_cv." + cv_file.filename.split('.')[-1]
    cv_file.save(temp_path)

    try:
        cv_text = read_cv(temp_path)
    except Exception as e:
        os.remove(temp_path)
        return jsonify({"error": str(e)}), 400

    # Compute similarity
    similarity = compute_similarity(job_text, cv_text)
    embed_percent = round(similarity * 100, 2)

    # Predict category
    category_pred = clf.predict([cv_text])[0]
    confidence = round(max(clf.predict_proba([cv_text])[0]) * 100, 2)

    # Hybrid score
    final_score = round((0.8 * embed_percent) + (0.2 * confidence), 2)

    os.remove(temp_path)

    return jsonify({
        "match_score": final_score,
        "cv_category": category_pred,
        "embedding_similarity": embed_percent,
        "ml_confidence": confidence
    })


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8002, debug=True)
