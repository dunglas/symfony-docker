# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker-compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## Fix Chrome/Brave SSL

If you have a TLS trust issues, you can copy the self-signed certificate from Caddy and add it to the trusted certificates :

    # Mac
    $ docker cp $(docker-compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
    # Linux
    $ docker cp $(docker-compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates

## HTTPs and Redirects

If Symfony is generating an internal redirect for an `https://` url, but the resulting url is `http://`, you have to uncomment the `TRUSTED_PROXIES` setting in your `.env` file.
For more details see the [Symfony internal redirect documentation](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-a-route).
