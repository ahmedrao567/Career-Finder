# filter_logs.py

ERROR_KEYWORDS = [
    "error",
    "failed",
    "failure",
    "exception",
    "traceback",
    "timeout",
    "permission denied",
    "connection refused",
    "404",
    "500",
    "warning",
    "unhealthy",
    "panic",
    "oom",
    "killed",
    "no such file or directory",
    "module not found",
    "exited"
]


def filter_logs(logs):

    filtered_logs = []

    for line in logs.splitlines():

        line_lower = line.lower()

        if any(keyword in line_lower for keyword in ERROR_KEYWORDS):
            filtered_logs.append(line)

    return filtered_logs