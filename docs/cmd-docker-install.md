# `install` command

This command's goal is to help you setup the most efficient Docker setup on OSX.

## Usage

```
dock-cli docker:install
```

## What is it doing ?

It install, if not already installed, the following software:
- Homebrew
- Homebrew Cask
- VirtualBox
- Vargant
- Dinghy (using boot2docker, but uses NFS sharing instead of vboxsf)
- Docker Compose
- PHP SSH2 extension

Then, it configures your routing to have a direct access to Docker containers running on Dinghy. That means that you
**won't** need to have port mappings to your host, just use the container(s) IP(s), which allows you to have many containers
exposing the same port.

The next step is to configure a DNS resolution for your project. [DnsDock](https://github.com/tonistiigi/dnsdock) is
configured to start each time Docker starts, and provides you a DNS server to lookup for running containers. OSX resolver
is then configured to use it for domain names ending with `.docker`.
