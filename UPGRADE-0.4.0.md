# UPGRADE from 0.3.x to 0.4.x

*Note:* there's nothing new on Linux systems.

The release 0.4.0 of `dock-cli` is embracing the new Dinghy release which is using docker-machine to maintain the
Virtual Machine running Docker for OSX systems.

Before upgrading, we'll need to run the following commands:
```
dinghy halt
dinghy destroy
```

Then, run the installer again:
```
dock-cli docker:install
```

