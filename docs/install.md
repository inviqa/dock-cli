# Installation

Download the last PHAR release:
```
wget https://inviqa.github.io/dock-cli/downloads/dock-cli-latest.phar -O dock-cli
chmod +x ./dock-cli
sudo mv dock-cli /usr/bin/dock-cli
```

## Install Docker on your system

To install the Docker development environment run:

```
dock-cli docker:install
```

**Note:** This sets environment variables which are loaded for the current shell and any opened after running it.
If you have any other shells already opened you will need to `source` these variables before running the other commands.
If you see an error message similar to `Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?`
when running subsequent commands then this may be the issue.
