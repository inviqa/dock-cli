# `docker:install` command

This command's goal is to help you setup the most efficient Docker environment.

## Usage

```
dock-cli docker:install
```

## What does it do on a Mac?

It installs, if not already installed, the following software:
- Homebrew
- Homebrew Cask
- VirtualBox
- Vargant
- Docker-Machine
- Docker Compose
- PHP SSH2 extension

Then, it configures your routing to have a direct access to Docker containers running on the VM. That means that you
**won't** need to have port mappings to your host, just use the container(s) IP(s), which allows you to have many containers
exposing the same port.

The next step is to configure DNS resolution for your project. [DnsDock](https://github.com/aacebedo/dnsdock) is
configured to start each time Docker starts, and provides you a DNS server to lookup for running containers. OSX resolver
is then configured to use it for domain names ending with `.docker`.

## What does it do on Linux?

On Linux, it installs, if not already installed, the following software:
- Docker
- Docker Compose

Then it performs some more tasks:
- adds your current `$USER` to the `docker` group to be able to run docker without sudo
- starts the tonistiigi/dnsdock container, binds it to `172.17.42.1`
- adds `172.17.42.1` as a DNS server
