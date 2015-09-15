# Extra host names plugins

The idea of this plugin is to reproduce a feature that is extremely useful using Vagrant: fixed arbitrary domain names
for the running VMs. The interest of this feature come from the "easy-to-use" point of view to the fact that it is
needed using OAuth2 servers to have a fixed address for instance.

In order to configure the domain names of your components, you'll have to put this configuration in your project's
`composer.json` file.

If you have a `docker-compose.yml` file that defines the components `api` and `ui` for instance, you can then configure
domain names like that in your `composer.json` file:

```json
{
    "extra": {
        "dock-cli": {
            "extra-hostname": {
                "api": ["my-api.acme.tld", "local.api.acme.tld"],
                "ui": ["local.acme.tld"]
            }
        }
    }
}
```

Each time you'll run the `dock-cli start` command, a corresponding line in your `/etc/hosts` file will be created or
updated with the IP address of the running container.
