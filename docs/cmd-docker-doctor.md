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

| Test name             | Test description                            | Automatic fix provided |
| --------------------- | ------------------------------------------- | ---------------------- |
| Docker version        | Verify docker command exists                | Yes                    |
| Docker info           | Verify docker is running                    | Yes                    |
| Ping docker           | Verify we can ping docker network interface | Yes                    |
| Dnsdock running       | Verify dnsdock container is running         | Yes                    |
| Dnsdock as DNS server | Verify dnsdock is used as a DNS server      | Yes                    |
