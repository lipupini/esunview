# Display list of commands/recipes
default:
	just --list

# ---

# An idea for a way to grep a collection ActivityPub inbox
inbox account:
	ls -l --color=auto --format=single-column collection/{{account}}/.lipupini/inbox

# Proxy to system `justfile`
system *args="":
	cd system && just {{args}}

# Proxy to test `justfile`
test *args="":
	cd test && just {{args}}

# Proxy to deploy `justfile`
deploy *args="":
	cd system/deploy && just {{args}}

# Proxy to docker `justfile`
docker *args="":
	cd system/deploy/docker && just {{args}}
