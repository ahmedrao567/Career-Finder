# knowledge_base.py

SOLUTIONS = {

    "connection refused":
    """
    Possible Causes:
    - Service is not running.
    - Wrong port mapping.
    - Health endpoint is incorrect.

    Suggested Fixes:
    - Check docker compose ps
    - Check exposed ports
    - Verify the health check URL
    """,

    "permission denied":
    """
    Possible Causes:
    - Incorrect SSH key.
    - Incorrect file permissions.

    Suggested Fixes:
    - Verify EC2_SSH_KEY.
    - Verify SSH access manually.
    """,

    "no such file or directory":
    """
    Possible Causes:
    - Incorrect deployment directory.

    Suggested Fixes:
    - Verify the EC2 deployment path.
    """,

    "timeout":
    """
    Possible Causes:
    - Slow container startup.

    Suggested Fixes:
    - Increase health check wait time.
    - Verify container status.
    """,

    "404":
    """
    Possible Causes:
    - Invalid health endpoint.

    Suggested Fixes:
    - Verify the application's health endpoint.
    """
}