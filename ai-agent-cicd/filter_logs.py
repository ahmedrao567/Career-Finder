ERROR_KEYWORDS = [

    "error",
    "failed",
    "exception",
    "traceback",
    "timeout",
    "permission denied",
    "connection refused",
    "404",
    "500",
    "panic",
    "unhealthy",
    "oom",
    "killed"

]


def filter_logs(logs):

    important_lines = []

    for line in logs.splitlines():

        lower_line = line.lower()

        if any(keyword in lower_line for keyword in ERROR_KEYWORDS):
            important_lines.append(line)

    return important_lines