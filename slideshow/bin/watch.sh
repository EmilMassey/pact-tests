#/bin/bash

docker run --rm --init -v "$(pwd)/$(dirname "$0")/..:/home/marp/app/" -e MARP_USER="$(id -u):$(id -g)" marpteam/marp-cli slideshow.md -w