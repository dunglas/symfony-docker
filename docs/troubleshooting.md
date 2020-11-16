# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker-compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## Fix Chrome/Brave SSL

If you have a SSL trust issues, download the self-signed certificate and run :

    $ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /path/to/you/certificate.cer

## HTTPs and Redirects
When Symfony is generating an internal redirect, and you're browsing via `https://...` you're redirected to `http://`.
To fix this issue uncomment the `TRUSTED_PROXIES` setting in your .env file (see https://github.com/dunglas/symfony-docker/issues/68)
