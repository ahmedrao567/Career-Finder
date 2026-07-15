from docker_client import get_containers

containers = get_containers()

for c in containers:
    print(c)