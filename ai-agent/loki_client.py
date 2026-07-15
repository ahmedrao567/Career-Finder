import requests

LOKI = "http://localhost:3100"

def get_logs():

    query = '{container=~".+"}'

    url = f"{LOKI}/loki/api/v1/query_range"

    params = {
        "query": query,
        "limit": 20
    }

    r = requests.get(url, params=params)

    data = r.json()

    return data["data"]["result"]