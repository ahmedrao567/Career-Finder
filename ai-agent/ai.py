import ollama


# -----------------------------------------
# Error / Incident Keywords
# -----------------------------------------

ERROR_KEYWORDS = [

    "error",
    "exception",
    "traceback",
    "critical",
    "failed",
    "failure",
    "timeout",
    "refused",
    "crash",
    "terminated",
    "panic",
    "warning",
    "unhealthy",
    "oom",
    "killed",
    "restart",
    "restarting",
    "not found",
    "503",
    "500",
    "404",
    "stopped",
    "shutdown",
    "unavailable",
    "denied",
    "connection refused",
    "exited",
    "dead"

]


# -----------------------------------------
# Interesting Log Filter
# -----------------------------------------

def is_interesting_log(log):

    """
    Returns True if the log looks like
    an incident or warning.
    """

    log = log.lower()

    return any(keyword in log for keyword in ERROR_KEYWORDS)


# -----------------------------------------
# Reduce Log Size
# -----------------------------------------

def limit_log_size(log, max_lines=8):

    """
    Sends only the latest few log lines
    to the LLM.
    """

    lines = log.splitlines()

    if len(lines) > max_lines:
        lines = lines[-max_lines:]

    return "\n".join(lines)


# -----------------------------------------
# AI Log Analysis
# -----------------------------------------

def analyze(log):

    """
    Analyse logs using Ollama + Llama 3.
    """

    # Skip healthy logs

    if not is_interesting_log(log):

        return """
Issue Summary:
No issues detected.

Severity:
LOW

Root Cause:
The log contains normal informational messages.

Recommended Solution:
No action is required.

Next Troubleshooting Steps:
Continue monitoring the application.

Impact:
No impact detected.
"""


    # Reduce log size for faster inference

    log = limit_log_size(log)


    # -------------------------------------
    # Prompt
    # -------------------------------------

    prompt = f"""

You are a Senior DevOps Engineer and Site Reliability Engineer (SRE).

Analyse the following Docker, Kubernetes, application or infrastructure log.

Your responsibilities are:

- Detect failures.
- Detect unhealthy services.
- Detect container crashes.
- Detect shutdowns.
- Detect connectivity issues.
- Detect application errors.
- Detect API failures.
- Detect database issues.
- Detect restart loops.
- Detect resource problems if present.


Provide ONLY the following sections:


1. Issue Summary

2. Severity
(LOW, MEDIUM, HIGH or CRITICAL)

3. Root Cause

4. Recommended Solution

5. Next Troubleshooting Steps

6. Impact


Rules:

- Keep the response concise.
- Use practical DevOps recommendations.
- If a container or service is down, explicitly mention it.
- If the issue is minor, clearly state that.
- Do not add unnecessary explanations.


Log:

{log}

"""


    # -------------------------------------
    # Ollama Request
    # -------------------------------------

    try:

        response = ollama.chat(

            model="llama3",

            messages=[

                {
                    "role": "user",
                    "content": prompt
                }

            ]

        )

        return response["message"]["content"]


    # -------------------------------------
    # Error Handling
    # -------------------------------------

    except Exception as e:

        return f"""
Issue Summary:
AI analysis failed.

Severity:
MEDIUM

Root Cause:
Unable to communicate with the Ollama server or model.

Recommended Solution:
Verify that Ollama is running and the selected model is installed.

Next Troubleshooting Steps:

- Run: ollama serve
- Run: ollama list
- Verify that llama3 is installed.
- Verify that the Ollama server is reachable.

Impact:
AI incident analysis could not be generated.

Error:

{str(e)}
"""