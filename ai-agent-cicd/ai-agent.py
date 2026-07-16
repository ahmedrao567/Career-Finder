# ai_agent.py

import sys

from filter_logs import filter_logs
from knowledge_base import SOLUTIONS


def generate_report(filtered_logs):

    print("\n==============================")
    print("AI FAILURE ANALYSIS REPORT")
    print("==============================\n")

    if not filtered_logs:
        print("No errors detected.")
        return

    print("Detected Issues:\n")

    for line in filtered_logs:
        print(line)

    print("\n------------------------------")
    print("Suggested Fixes")
    print("------------------------------\n")

    for line in filtered_logs:

        lower_line = line.lower()

        for key in SOLUTIONS:

            if key in lower_line:
                print(SOLUTIONS[key])


def main():

    if len(sys.argv) != 2:
        print("Usage: python ai_agent.py <logs_file>")
        return

    file_name = sys.argv[1]

    with open(file_name, "r") as f:
        logs = f.read()

    filtered_logs = filter_logs(logs)

    generate_report(filtered_logs)


if __name__ == "__main__":
    main()