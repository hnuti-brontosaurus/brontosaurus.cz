#!/usr/bin/env bash

# Předpokládá se, že se skript spouští z kořenové složky repozitáře.
if [ ! -e "deploy/deploy.sh" ]; then
	echo "You have to run this script from the repository root directory!"
	exit 1
fi

# Ověřit, že byl předán parametr s cestou.
if [ -z "$1" ]; then
	echo "You have to set the remote directory (\`wp-content/themes\` in your WP installation), where the plugin should be uploaded to."
	exit 1
fi

# Nastavit vzdálenou složku.
REMOTE_PATH="$1"

# Nahrát tuto složku do vzdálené složky.
rsync -rvP "deploy/files/" "${REMOTE_PATH}"
