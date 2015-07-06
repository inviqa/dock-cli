# Dock CLI

This CLI application provides an abstraction layer for Docker-based projects.

**Note:** right now, it's focused exclusively on OSX.

## Getting started

Download the last PHAR release:
```
wget http://sroze.github.io/dock-cli/downloads/dock-cli-1.0.0.phar -O dock-cli
chmod +x ./dock-cli
sudo mv dock-cli /usr/bin/dock-cli
```

To install the Docker development environment run:

```
dock-cli docker-install
```

**Note:** You will need to restart the terminal after this before using it to start a project.

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

- [`up` command](docs/cmd-up.md) starts the Docker environment for the project.

## Update

To update this tool, simply run the `self-update` command:

```
dock-cli self-update
```