#!/usr/bin/env bash

# Assuming this script is run from repository root directory.
if [ ! -e "deploy/deploy.sh" ]; then
	echo "You have to run this script from the repository root directory!"
	exit 1
fi

# Check that path parameter has been passed.
if [ -z "$1" ]; then
	echo "You have to set the remote directory (\`wp-content/themes\` in your WP installation), where the plugin should be uploaded to."
	exit 1
fi

# Setting remote directory
REMOTE_PATH="$1"

# upload this directory into remote directory
rsync -rvP "deploy/files/" "${REMOTE_PATH}"
