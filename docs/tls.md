# TLS Certificates

## Trusting the Authority

With a standard installation, the authority used to sign certificates generated in the Nginx container is not trusted by your local machine.
You must add the authority to the trust store of the host :

```
# Mac
$ docker cp $(docker compose ps -q nginx_dev):/srv/app/cert.pem /tmp/laravel-docker-cert.pem && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/laravel-docker-cert.pem
# Linux
$ docker cp $(docker compose ps -q nginx_dev):/srv/app/cert.pem /usr/local/share/ca-certificates/laravel-docker-cert.pem && sudo update-ca-certificates
# Windows
$ docker compose cp nginx_dev:/srv/app/cert.pem %TEMP%/laravel-docker-cert.crt && certutil -addstore -f "ROOT" %TEMP%/laravel-docker-cert.crt
```
