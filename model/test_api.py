import requests

# Flask API URL
url = "http://127.0.0.1:8001/match-score"

# Job description as text
job_text = """
We are looking for a DevOps Engineer with experience in CI/CD, Docker, AWS, and Linux automation.
Responsibilities include deployment pipelines, monitoring, and infrastructure as code.
"""

# CV file path (DOCX or PDF)
cv_file_path = r"C:\Users\munee\Downloads\resume.docx"  # update your path

# Prepare files for POST
files = {
    "cv_file": open(cv_file_path, "rb")
}

data = {
    "job_text": job_text
}

# Send POST request
response = requests.post(url, data=data, files=files)
print(response.json())
