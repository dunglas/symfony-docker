# TLS Certificates

## Trusting the Authority

With a standard installation, the authority used to sign certificates generated in the Caddy container is not trusted by your local machine.
You must add the authority to the trust store of the host :

```
# Mac
$ docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
# Linux
$ docker cp $(docker compose ps -q php):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
# Windows
$ docker compose cp php:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```

## Using Custom TLS Certificates

By default, Caddy will automatically generate TLS certificates using Let's Encrypt or ZeroSSL.
But sometimes you may prefer using custom certificates.

For instance, to use self-signed certificates created with [mkcert](https://github.com/FiloSottile/mkcert) do as follows:

1. Locally install `mkcert`
2. Create the folder storing the certs: 
   `mkdir frankenphp/certs -p`
3. Generate the certificates for your local host (example: "server-name.localhost"):
   `mkcert -cert-file frankenphp/certs/tls.pem -key-file frankenphp/certs/tls.key "server-name.localhost"`
4. Add these lines to the `./compose.override.yaml` file about `CADDY_SERVER_EXTRA_DIRECTIVES` environment and volume for the `php` service :
    ```diff
    php:
      environment:
    +    CADDY_SERVER_EXTRA_DIRECTIVES: "tls /etc/caddy/certs/tls.pem /etc/caddy/certs/tls.key"
        # ...
      volumes:
    +    - ./frankenphp/certs:/etc/caddy/certs:ro
        - ./public:/app/public:ro
    ```
5. Restart your `php` service
