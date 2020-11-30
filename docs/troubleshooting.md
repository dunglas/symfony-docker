# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker-compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## Fix Chrome/Brave SSL

If you have a SSL trust issues, download the self-signed certificate and run :

    $ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /path/to/you/certificate.cer

## HTTPs and Redirects

If Symfony is generating an internal redirect for an `https://` url, but the resulting url is `http://`, you have to uncomment the `TRUSTED_PROXIES` setting in your `.env` file.
For more details see the [Symfony internal redirect docuentation](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-a-route).
