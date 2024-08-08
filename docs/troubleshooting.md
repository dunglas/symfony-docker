# Troubleshooting

## Editing Permissions on Linux

If you work on linux and cannot edit some of the project files right after the first installation, you can run `docker compose run --rm php chown -R $(id -u):$(id -g) .` to set yourself as owner of the project files that were created by the docker container.

## TLS/HTTPS Issues

See more in the [TLS section](tls.md)
