import ollama


def analyze(log):

    prompt = f"""
You are a Senior DevOps Engineer.

Analyze the following log.

Provide:

1. Root cause
2. Severity (Low / Medium / High / Critical)
3. Solution
4. Recommended next troubleshooting steps

Log:

{log}
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