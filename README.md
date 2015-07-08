# Dock CLI

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sroze/dock-cli/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sroze/dock-cli/?branch=master)

This CLI application provides an abstraction layer for Docker-based projects.

**Note:** right now, it's focused exclusively on OSX.

## Getting started

Download the last PHAR release:
```
wget http://sroze.github.io/dock-cli/downloads/dock-cli-latest.phar -O dock-cli
chmod +x ./dock-cli
sudo mv dock-cli /usr/bin/dock-cli
```

To install the Docker development environment run:

```
dock-cli docker-install
```

**Note:** This sets environment variables which are loaded for the current shell and any opened after running it.
If you have any other shells already opened you will need to `source` these variables before running the other commands.
If you see an error message similar to `Couldn't connect to Docker daemon at http+docker://localunixsocket - is it running?`
when running subsequent commands then this may be the issue.

You can now start up a specific project by running the `up` command in the project directory:

```
dock-cli up
```

## Commands

The following commands are available:

### System commands

- [`docker:install` command](docs/cmd-docker-install.md) sets up your Docker development environment.
- [`docker:restart` command](docs/cmd-docker-restart.md) restarts your Docker VM.

### Project commands

- [`start` command](docs/cmd-start.md) starts the Docker environment for the project.
- [`stop` command](docs/cmd-stop.md) stops the running Docker containers of the project.
- [`ps` command](docs/cmd-ps.md) list all the project containers if any.
- [`logs` command](docs/cmd-logs.md) follows the logs of all or a given container.

## Update

To update this tool, simply run the `self-update` command:

```
dock-cli self-update
```
