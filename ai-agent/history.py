history = []

MAX_HISTORY = 50


def add_history(log, analysis):

    for item in history:

        if item["log"] == log:
            return

    history.append({

        "log": log,
        "analysis": analysis

    })

    if len(history) > MAX_HISTORY:
        history.pop(0)


def get_history():
    return history