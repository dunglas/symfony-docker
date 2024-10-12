# Supervisor
Supervisor is a powerful process control system that enables running and managing multiple processes within a Docker container. 
When building Docker images, Supervisor provides a robust solution for orchestrating various services or applications in a single container.
By incorporating Supervisor into your Dockerfile, you can efficiently launch and monitor multiple processes, simplifying container management and enhancing overall functionality. 
This approach is particularly useful when your application requires several interdependent services to run concurrently within the same container environment.

We can also execute multiple Symfony message consumers with the supervisor.
## Docker Configuration
* Create a new folder as docker (if you want, you can change the folder name)
* Create a supervisor configuration ([More detail](http://supervisord.org/configuration.html))
  ```conf
  [rpcinterface:supervisor]
  supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

  [supervisorctl]

  [inet_http_server]
  port = 127.0.0.1:9001

  [supervisord]
  nodaemon=true
  logfile=/var/log/supervisord.log
  pidfile=/tmp/supervisord.pid
  nocleanup=false
  user=root
  ```
* And then you can add your command end of the configuration file or can create new conf file. 
For a instance with symfony message consumer supervisor.conf looks like
  ```conf
  [rpcinterface:supervisor]
  supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

  [supervisorctl]

  [inet_http_server]
  port = 127.0.0.1:9001

  [supervisord]
  nodaemon=true
  logfile=/var/log/supervisord.log
  pidfile=/tmp/supervisord.pid
  nocleanup=false
  user=root

  [program:messenger-consume-async]
  command=php bin/console messenger:consume async -vv --limit=10 --memory-limit=128M --time-limit=3600
  numprocs=1
  startsecs=1
  autostart=true
  autorestart=true
  process_name=%(program_name)s_%(process_num)02d
  user=root
  ```

* Add new service to compose.yaml
  ```yaml
  supervisor:
    image: ${IMAGES_PREFIX:-}app-php
    build:
      context: .
      target: frankenphp_base
    environment:
      SYMFONY_VERSION: ${SYMFONY_VERSION:-}
      STABILITY: ${STABILITY:-stable}
      SUPERVISOR: true
    restart: unless-stopped
    depends_on:
      - php
    volumes:
      - ./:/app
  ```

* Change your Dockerfile
  ```diff
  RUN apt-get update && apt-get install -y --no-install-recommends \
	  acl \
	  file \
	  gettext \
	  git \
  +  supervisor \
	  && rm -rf /var/lib/apt/lists/*
  ```

* Change frankenphp/docker-entrypoint.sh
  ```diff
  +if [ "$SUPERVISOR" = "true" ]; then
  +    cp /app/queue/supervisord.conf /etc/supervisord.conf
  +    exec supervisord -n -c /etc/supervisord.conf
  +fi
  
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
  ```
  If you want to avoid migration multiple times, you can check [this link](https://github.com/dunglas/symfony-docker/issues/539#issuecomment-2051258892)

## Final steps
Rebuild the docker environment:
```shell
docker compose down --remove-orphans && docker compose build --pull --no-cache
```

Start the services:
```shell
docker compose up -d
```
