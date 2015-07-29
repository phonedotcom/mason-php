#!/bin/bash

ORIG_DIR=$(pwd)
PROJECT="$(git rev-parse --show-toplevel)"
cd "$PROJECT"

EXPECTED="../hooks"
ACTUAL=$(readlink "$PROJECT/.git/hooks")

if [ "$EXPECTED" != "$ACTUAL" ]; then

	echo >/dev/stderr
	echo "Making .git/hooks/ a symlink to hooks/" >/dev/stderr

	cd .git || exit 1
	if [ -e hooks ]; then
		BACKUP=hooks-$(date +%s)
		mv hooks $BACKUP || exit 1
		echo "NOTE: Your existing .git/hooks has been moved to .git/$BACKUP" > /dev/stderr
	fi
	ln -s ../hooks hooks || exit 1
	cd ..

	echo >/dev/stderr
fi

cd "$ORIG_DIR"
