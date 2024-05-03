# This list of commands
help:
	just --list

# ---

# An idea for a way to grep a collection ActivityPub inbox
inbox account:
	ls -l --color=auto --format=single-column collection/{{account}}/.lipupini/inbox

# Proxy to `system/justfile`
# System commands
system *args="":
	cd system && just {{args}}

# Proxy to `test/justfile`
# E2E testing commands
test *args="":
	cd test && just {{args}}

# Proxy to `system/deploy/justfile`
# Deployment commands
deploy *args="":
	cd system/deploy && just {{args}}

# Proxy to `system/deploy/docker/justfile`
# Docker commands
docker *args="":
	cd system/deploy/docker && just {{args}}
