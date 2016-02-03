# `build` command

This command builds and resets a set of containers. It will run `docker-compose build [containers]` and then 
run the reset command against those containers.

It is extremely useful for when the configuration of the running process needs to be updated for
instance, as building the container will not restart it and it won't load the new configuration.

## Usage

```
dock-cli build [container-name] ...
```

If no container name is given, all the container will be built and reset. Be careful because it'll also remove
persistent data of containers even if they have (non-mapped) volumes.
