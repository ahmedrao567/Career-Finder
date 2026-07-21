# ai-agent.py

import sys
import ollama
from filter_logs import filter_logs


def analyze_logs(logs):

    prompt = f"""
You are a DevOps CI/CD failure analysis assistant.

The text below contains deployment failure logs from a CI/CD pipeline.

Your job is to:

1. Identify the actual error(s).
2. Ignore unnecessary warnings unless they caused the failure.
3. Explain the root cause of the failure.
4. Suggest possible fixes.

IMPORTANT:
- Do NOT ask the user to provide more logs.
- Do NOT ask any questions.
- Analyze ONLY the logs provided below.

Deployment Logs:

{logs}
"""

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


def main():

    if len(sys.argv) != 2:
        print("Usage:")
        print("python ai-agent.py logs.txt")
        return

    file_name = sys.argv[1]

    # Read complete logs
    with open(file_name, "r") as file:
        logs = file.read()

    # Filter only relevant log lines
    filtered_logs = filter_logs(logs)

    # Convert list into a string
    filtered_logs = "\n".join(filtered_logs)

    # If nothing was detected
    if not filtered_logs:
        print("\nNo deployment errors were detected in the logs.")
        return

    # Send filtered logs to Llama3
    result = analyze_logs(filtered_logs)

    print("\n==============================")
    print("AI FAILURE ANALYSIS REPORT")
    print("==============================\n")

    print(result)


if __name__ == "__main__":
    main()