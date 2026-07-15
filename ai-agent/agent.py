from loki_client import get_logs
from ai import analyze

logs = get_logs()

if not logs:
    print("No logs found.")
    exit()

print(f"Found {len(logs)} log stream(s).\n")

for stream in logs:

    values = stream["values"]

    for value in values:

        log = value[1]

        print("=" * 60)
        print("LOG")
        print("=" * 60)
        print(log)

        print("\nAI ANALYSIS\n")

        answer = analyze(log)

        print(answer)
        print("\n")