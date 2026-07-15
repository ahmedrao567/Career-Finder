from flask import Flask, render_template, jsonify

from docker_client import get_containers
from loki_client import get_logs
from ai import analyze

app = Flask(__name__)


# ==========================================================
# Dashboard
# ==========================================================

@app.route("/")
def home():
    return render_template("index.html")


# ==========================================================
# Docker Containers
# ==========================================================

@app.route("/api/containers")
def containers():

    try:
        data = get_containers()
        return jsonify(data)

    except Exception as e:
        return jsonify({
            "error": str(e)
        }), 500


# ==========================================================
# Loki Logs
# ==========================================================

@app.route("/api/logs")
def logs():

    try:

        streams = get_logs()

        output = []

        for stream in streams:
            for value in stream["values"]:

                output.append(value[1])

        output.reverse()

        return jsonify(output)

    except Exception as e:

        return jsonify({
            "error": str(e)
        }), 500


# ==========================================================
# AI Analysis
# ==========================================================

@app.route("/api/analysis")
def analysis():

    try:

        streams = get_logs()

        if not streams:

            return jsonify({
                "log": "",
                "analysis": "No logs found.",
                "recommendation": "Waiting for new logs..."
            })

        latest_log = streams[0]["values"][-1][1]

        ai_response = analyze(latest_log)

        return jsonify({

            "log": latest_log,

            "analysis": ai_response,

            "recommendation": "Review the AI recommendation and apply the suggested fix."

        })

    except Exception as e:

        return jsonify({

            "log": "",

            "analysis": f"Error: {str(e)}",

            "recommendation": "Unable to analyze logs."

        })


# ==========================================================
# Health Check
# ==========================================================

@app.route("/health")
def health():

    return jsonify({
        "status": "running"
    })


# ==========================================================
# Run Flask
# ==========================================================

if __name__ == "__main__":

    app.run(
        host="0.0.0.0",
        port=5000,
        debug=True
    )