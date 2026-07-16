from flask import Flask, render_template, jsonify

from docker_client import get_containers
from loki_client import get_logs
from ai import analyze, is_interesting_log
from history import add_history, get_history


app = Flask(__name__)


# -----------------------------------------
# Global Variables
# -----------------------------------------

LAST_ANALYZED_LOG = ""

ANALYZED_LOGS = {}


# -----------------------------------------
# Home Page
# -----------------------------------------

@app.route("/")
def home():

    return render_template("index.html")


# -----------------------------------------
# Containers API
# -----------------------------------------

@app.route("/api/containers")
def containers():

    try:

        data = get_containers()

        return jsonify(data)

    except Exception as e:

        return jsonify({

            "error": str(e)

        }), 500


# -----------------------------------------
# Live Logs API
# -----------------------------------------

@app.route("/api/logs")
def logs():

    try:

        streams = get_logs()

        output = []

        for stream in streams:

            for value in stream["values"]:

                output.append(value[1])

        # Latest logs first
        output.reverse()

        # Show only last 50 logs
        output = output[:50]

        return jsonify(output)

    except Exception as e:

        return jsonify({

            "error": str(e)

        }), 500


# -----------------------------------------
# AI Analysis API
# -----------------------------------------

@app.route("/api/analysis")
def analysis():

    global LAST_ANALYZED_LOG
    global ANALYZED_LOGS

    try:

        streams = get_logs()

        if not streams:

            return jsonify({

                "log": "",
                "analysis": "No logs found.",
                "recommendation": "Waiting for new logs..."

            })

        latest_log = streams[0]["values"][-1][1]


        # ---------------------------------
        # Healthy Logs
        # ---------------------------------

        if not is_interesting_log(latest_log):

            return jsonify({

                "log": latest_log,

                "analysis":
                "No issues detected. System appears healthy.",

                "recommendation":
                "No action required."

            })


        # ---------------------------------
        # Already Analyzed?
        # ---------------------------------

        if latest_log in ANALYZED_LOGS:

            return jsonify({

                "log": latest_log,

                "analysis":
                ANALYZED_LOGS[latest_log],

                "recommendation":
                "This incident has already been analysed."

            })


        # ---------------------------------
        # Same Log Again?
        # ---------------------------------

        if latest_log == LAST_ANALYZED_LOG:

            return jsonify({

                "log": latest_log,

                "analysis":
                "Waiting for new incidents.",

                "recommendation":
                "No new logs detected."

            })


        # ---------------------------------
        # New Incident Found
        # ---------------------------------

        ai_response = analyze(latest_log)


        # Store in memory

        ANALYZED_LOGS[latest_log] = ai_response

        LAST_ANALYZED_LOG = latest_log


        # Store in history

        add_history(

            latest_log,
            ai_response

        )


        return jsonify({

            "log": latest_log,

            "analysis":
            ai_response,

            "recommendation":
            "Please review the AI generated recommendations."

        })


    except Exception as e:

        return jsonify({

            "log": "",

            "analysis":
            f"AI Analysis Failed : {str(e)}",

            "recommendation":
            "Unable to analyse logs."

        })


# -----------------------------------------
# AI Incident History API
# -----------------------------------------

@app.route("/api/history")
def history():

    return jsonify(get_history())


# -----------------------------------------
# Health Check API
# -----------------------------------------

@app.route("/health")
def health():

    return jsonify({

        "status": "running"

    })


# -----------------------------------------
# Run Flask App
# -----------------------------------------

if __name__ == "__main__":

    app.run(

        host="0.0.0.0",
        port=5000,
        debug=True

    )