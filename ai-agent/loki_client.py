import requests
import time

LOKI = "http://localhost:3100"


def get_logs():

    now = int(time.time() * 1e9)
    five_minutes_ago = now - (5 * 60 * 1_000_000_000)

    query = '{container=~"career-finder-db-1|career-finder-student-web-1|career-finder-model-api-1"}'

    params = {
        "query": query,
        "start": five_minutes_ago,
        "end": now,
        "limit": 50,
        "direction": "BACKWARD"
    }

    response = requests.get(
        f"{LOKI}/loki/api/v1/query_range",
        params=params,
        timeout=10
    )

    response.raise_for_status()

    data = response.json()

    return data["data"]["result"]