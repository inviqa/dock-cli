# `reset` command

This command reset a set of containers. That will basically kill them, remove then and start
them again.

That's extremely useful when the configuration of the running process needs to be updated for
instance, as restarting it won't load the new configuration if the process to not support runtime
update.

## Usage

```
dock-cli reset [container-name] ...
```

If no container name is given, all the container will be reset. Be careful because it'll also remove
persistent data of containers even if they have (non-mapped) volumes.
