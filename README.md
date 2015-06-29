# Dock CLI

This CLI application provides an abstraction layer for Docker-based projects.

*Note:* right know, it's focused exclusively on OSX.

## Getting started

### Run from a release

You can download the PHAR archive from GitHub releases.

### Run from development

Clone this repository and install its dependencies:
```
composer install
```

Them, simply run the `app.php` file:
```
php app.php
```

## Commands

- [`install` command](docs/cmd-install.md), which setup your Docker development environment.
- [`restart` command](docs/cmd-restart.md), which restarts your Docker VM.
