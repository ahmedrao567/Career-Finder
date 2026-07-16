# ai_agent.py

from filter_logs import filter_logs

with open("compose_logs.log", "r") as f:
    logs = f.read()

errors = filter_logs(logs)

print("\nDetected Errors:\n")

for error in errors:
    print(error)