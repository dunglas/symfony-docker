# TLS Certificates

## Trusting the Authority

With a standard installation, the authority used to sign certificates generated in the Caddy container is not trusted by your local machine.
You must add the authority to the trust store of the host :

```
# Mac
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
# Linux
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
# Windows
$ docker compose cp caddy:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```

## Using Custom TLS Certificates

By default, Caddy will automatically generate TLS certificates using Let's Encrypt or ZeroSSL.
But sometimes you may prefer using custom certificates.

For instance, to use self-signed certificates created with [mkcert](https://github.com/FiloSottile/mkcert) do as follows:

1. Locally install `mkcert`
2. Create the folder storing the certs: 
   `mkdir docker/caddy/certs -p`
3. Generate the certificates for your local host (example: "server-name.localhost"):
   `mkcert -cert-file docker/caddy/certs/tls.pem -key-file docker/caddy/certs/tls.key "server-name.localhost"`
4. Add these lines to the `./docker-compose.override.yml` file about `CADDY_EXTRA_CONFIG` environment and volume for the `caddy` service :
    ```diff
    caddy:
    +  environment:
    +    CADDY_EXTRA_CONFIG: "tls /etc/caddy/certs/tls.pem /etc/caddy/certs/tls.key"
      volumes:
    +    - ./docker/caddy/certs:/etc/caddy/certs:ro
        - ./public:/srv/app/public:ro
    ```
5. Restart your `caddy` container
