# `docker:restart` command

This command simply restarts the virtual machine running Docker.

```
dock-cli docker:restart
```

## Available options

- `--memory=X` (or `-m X`) where `X` is the number of MB of memory you want to allocate to the Dinghy VM. This parameter will
  be persisted across restarts.

Right now, it's just stopping the Dinghy virtual machine and then starting it again with the recommended options.
