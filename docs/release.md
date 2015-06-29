# Release Dock CLI

First of all, tag the repository with the given target version:
```
git tag X.Y.Z
```

Create an phar archive of the application:
```
./vendor/bin/phar-composer build .
```

Push the tag, and join the created PHAR archive to the GitHub release.
