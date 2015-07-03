# Dock CLI

This CLI application provides an abstraction layer for Docker-based projects.

**Note:** right know, it's focused exclusively on OSX.

## Getting started

Download the last PHAR release:
```
wget http://sroze.github.io/dock-cli/downloads/dock-cli-0.2.1.phar -O dock-cli
chmod +x ./dock-cli
sudo mv dock-cli /usr/bin/dock-cli
```

## Update

Simply run the `self-update` command:
```
dock-cli self-update
```

## Commands

- [`install` command](docs/cmd-install.md), which setup your Docker development environment.
- [`restart` command](docs/cmd-restart.md), which restarts your Docker VM.
