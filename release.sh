#!/bin/bash

PHAR_NAME=dock-cli
MANIFEST_BASE_URL=http://inviqa.github.io/dock-cli
set -e


if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
vendor/kherge/box/bin/box build

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp $PHAR_NAME.phar downloads/$PHAR_NAME-${TAG}.phar
git add downloads/$PHAR_NAME-${TAG}.phar

SHA1=$(openssl sha1 $PHAR_NAME.phar | awk '{print $2}')

JSON='name:"'$PHAR_NAME'.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"${MANIFEST_BASE_URL}/downloads/${PHAR_NAME}-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
git add manifest.json

# Symlink latest version
rm downloads/$PHAR_NAME-latest.phar
cp downloads/$PHAR_NAME-${TAG}.phar downloads/$PHAR_NAME-latest.phar
git add downloads/$PHAR_NAME-latest.phar

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

echo "New version created. Now you should run:"
echo "git push origin gh-pages"
echo "git push ${TAG}"
