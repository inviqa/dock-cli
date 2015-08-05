# `docker:doctor` command

Diagnose problems with Docker setup and attempt to fix them.

## Usage

```
dock-cli docker:doctor --dry-run
dock-cli docker:doctor
```

## Available options

- `--dry-run` don't attempt to fix the problems, just show problems and suggest
  appropriate solutions

## What does it do?

- Test whether docker is installed and running.
- Test whether dnsdock container is running and whether it's used as one of the
  dns servers.
