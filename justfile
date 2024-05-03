# Display list of commands/recipes
default:
	just --list

# ---

# Open `system/config/state.php` in `vi`
config:
	printf '\n\n\nOpening `system/config/state.php` in `vi`...\n\n\n' && sleep 2.5 && vi system/config/state.php

# An idea for a way to grep a collection ActivityPub inbox
inbox account:
	ls -l --color=auto --format=single-column collection/{{account}}/.lipupini/inbox

# Start PHP's built-in webserver
serve frontend='Esunview' port='4000':
	cd module/{{frontend}}/webroot && PHP_CLI_SERVER_WORKERS=2 php -S localhost:{{port}} index.php

# Build a Lipupini Docker image from `system/docker`
docker-build type='frankenphp':
	docker-compose --file system/deploy/docker/{{type}}/docker-compose.yml build
	# docker build --tag lipupini/lipupini-{{type}}:latest --file system/deploy/docker/{{type}}/Dockerfile .

# Run Docker container from a Lipupini Docker image
docker-up type='frankenphp':
	docker-compose --file system/deploy/docker/{{type}}/docker-compose.yml up
	# docker run -it --rm --name lipupini/lipupini-{{type}}:latest lipupini-{{type}}

test *args:
	cd test && npx playwright test {{args}}

# Grab and integrate the latest version from remote `origin` https://github.com/lipupini/esunview.git
upgrade-deployment:
	#git reset --hard origin/master
	git fetch origin master
	git checkout origin/master
