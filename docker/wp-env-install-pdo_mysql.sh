#!/bin/bash
# wp-env's WordPress service images don't ship pdo_mysql; install it and reload
# Apache so PHP picks up the new extension.
#
# Each lookup is scoped to this project's containers via the compose service label
# plus the current directory's basename (wp-env derives its project/container names
# from the cwd). When concurrent wp-env configs share that basename prefix, only
# running containers are matched, and `head -n 1` selects the most-recently-created
# match among them. Both "wordpress" and "tests-wordpress" are checked because
# wp-env can run either one service or both.
set -euo pipefail

PROJECT_NAME="$(basename "$PWD")"
FOUND_CONTAINER=0

for SERVICE in wordpress tests-wordpress; do
	CONTAINER_ID="$(docker ps --filter "label=com.docker.compose.service=$SERVICE" --filter "name=$PROJECT_NAME" --filter status=running --format '{{.ID}}' | head -n 1)"

	if [ -z "$CONTAINER_ID" ]; then
		continue
	fi

	FOUND_CONTAINER=1
	docker exec "$CONTAINER_ID" docker-php-ext-install pdo_mysql
	docker exec "$CONTAINER_ID" service apache2 reload
done

if [ "$FOUND_CONTAINER" -eq 0 ]; then
	echo "wp-env-install-pdo_mysql.sh: no running wordpress or tests-wordpress container found for $PROJECT_NAME" >&2
	exit 1
fi
