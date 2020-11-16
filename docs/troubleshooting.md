# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker-compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## Fix Chrome/Brave SSL

If you have a SSL trust issues, you can copy the self-signed certificate from caddy and add it to the trusted certificates :

    # MacOS
    $ docker cp $(docker-compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
    # Linux
    $ docker cp $(docker-compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
