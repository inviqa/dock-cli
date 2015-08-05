# `run` command

Run a command on a service. If `pwd` is in the build path of one of the
`docker-compose.yml` services, run on that service, otherwise ask.

## Usage

```
dock-cli run cat /etc/passwd
dock-cli run --service=mysql cat /etc/passwd
```

## Available options

- `--service=SERVICE` (or `-s SERVICE`) where `SERVICE` is the name of a
  service from `docker-compose.yml`.
