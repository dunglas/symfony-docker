## Specifiy Build directory

If you want to place this repository in a subdirectory you can specify the `BUILD_ARGS` in your `compose.prod.yaml` AND `compose.override.yaml`.

```yaml
services:
  php:
    build:
      context: . # <-- Adapt the context to the root level of your project
      target: frankenphp_dev
      args:
          BUILD_DIR: "docker" # <-- Specifiy the subdirectory where this repository is placed
    volumes:
      - ./:/app # <-- Adapt the mount volume to the directory specified 
```
