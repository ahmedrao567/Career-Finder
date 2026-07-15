import docker

client = docker.from_env()

def get_containers():

    containers = []

    for c in client.containers.list():

        # Only Career Finder containers
        if not c.name.startswith("career-finder-"):
            continue

        containers.append({
            "id": c.short_id,
            "name": c.name,
            "image": c.image.tags[0] if c.image.tags else c.image.short_id,
            "status": c.status
        })

    return containers