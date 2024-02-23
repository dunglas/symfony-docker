# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## HTTPs and Redirects

If Symfony is generating an internal redirect for an `https://` url, but the resulting url is `http://`, you have to uncomment the `TRUSTED_PROXIES` setting in your `.env` file and add this line in `config/packages/framework.yaml`:
```
# config/packages/framework.yaml
framework:
    trusted_proxies: '%env(TRUSTED_PROXIES)%'
```

For more details see the [Symfony internal redirect documentation](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-a-route).

## TLS/HTTPS Issues

See more in the [TLS section](tls.md)
