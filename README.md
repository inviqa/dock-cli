# Dock CLI

This CLI application provides an abstraction layer for Docker-based projects.

**Note:** right know, it's focused exclusively on OSX.

## Getting started

Download the last PHAR release:
```
wget http://sroze.github.io/dock-cli/downloads/dock-cli-1.0.0.phar -O dock-cli
chmod +x ./dock-cli
sudo mv dock-cli /usr/bin/dock-cli
```

## Update

Simply run the `self-update` command:
```
dock-cli self-update
```

## Commands

### System commands

- [`docker:install` command](docs/cmd-docker-install.md) sets up your Docker development environment.
- [`docker:restart` command](docs/cmd-docker-restart.md) restarts your Docker VM.

### Project commands

- [`up` command](docs/cmd-up.md) starts the Docker environment for the project.
