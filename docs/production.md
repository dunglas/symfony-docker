# Deploying in Production

Symfony Docker provides Docker images, and a Docker Compose definition optimized for production usage.
In this tutorial, we will learn how to deploy our Symfony application on a single server using Docker Compose.

## Preparing a Server

To deploy your application in production, you need a server.
In this tutorial, we will use a virtual machine provided by DigitalOcean, but any Linux server can work.
If you already have a Linux server with Docker Compose installed, you can skip straight to [the next section](#configuring-a-domain-name).

Otherwise, use [this affiliate link](https://m.do.co/c/5d8aabe3ab80) to get $100 of free credit, create an account, then click on "Create a Droplet".
Then, click on the "Marketplace" tab under the "Choose an image" section and search for the app named "Docker".
This will provision an Ubuntu server with the latest versions of Docker and Docker Compose already installed!

For test purposes, the cheapest plans will be enough, even though you might want at least 2GB of RAM to execute Docker Compose for the first time. For real production usage, you'll probably want to pick a plan in the "general purpose" section to fit your needs.

![Deploying a Symfony app on DigitalOcean with Docker Compose](digitalocean-droplet.png)

You can keep the defaults for other settings, or tweak them according to your needs.
Don't forget to add your SSH key or create a password then press the "Finalize and create" button.

Then, wait a few seconds while your Droplet is provisioning.
When your Droplet is ready, use SSH to connect:

```console
ssh root@<droplet-ip>
```

## Configuring a Domain Name

In most cases, you'll want to associate a domain name with your site.
If you don't own a domain name yet, you'll have to buy one through a registrar.

Then create a DNS record of type `A` for your domain name pointing to the IP address of your server:

```dns
your-domain-name.example.com.  IN  A     207.154.233.113
```

Example with the DigitalOcean Domains service ("Networking" > "Domains"):

![Configuring DNS on DigitalOcean](digitalocean-dns.png)

> [!NOTE]  
> Let's Encrypt, the service used by default by Symfony Docker to automatically generate a TLS certificate doesn't support using bare IP addresses. Using a domain name is mandatory to use Let's Encrypt.

## Deploying

Copy your project on the server using `git clone`, `scp`, or any other tool that may fit your need.
If you use GitHub, you may want to use [a deploy key](https://docs.github.com/en/free-pro-team@latest/developers/overview/managing-deploy-keys#deploy-keys).
Deploy keys are also [supported by GitLab](https://docs.gitlab.com/ee/user/project/deploy_keys/).

Example with Git:

```console
git clone git@github.com:<username>/<project-name>.git
```

Go into the directory containing your project (`<project-name>`), and start the app in production mode:

```console
SERVER_NAME=your-domain-name.example.com \
APP_SECRET=ChangeMe \
CADDY_MERCURE_JWT_SECRET=ChangeThisMercureHubJWTSecretKey \
docker compose -f compose.yaml -f compose.prod.yaml up -d --wait
```

Be sure to replace `your-domain-name.example.com` with your actual domain name and to set the values of `APP_SECRET`, `CADDY_MERCURE_JWT_SECRET` to cryptographically secure random values.

Your server is up and running, and a HTTPS certificate has been automatically generated for you.
Go to `https://your-domain-name.example.com` and enjoy!

> [!NOTE]
> The worker mode of FrankenPHP is enabled by default in prod. To disable it, add the env var FRANKENPHP_CONFIG as empty to the compose.prod.yaml file.

> [!CAUTION]
> Docker can have a cache layer, make sure you have the right build for each deployment or rebuild your project with --no-cache option to avoid cache issue.

## Disabling HTTPS

Alternatively, if you don't want to expose an HTTPS server but only an HTTP one, run the following command:

```console
SERVER_NAME=:80 \
APP_SECRET=ChangeMe \
CADDY_MERCURE_JWT_SECRET=ChangeThisMercureHubJWTSecretKey \
docker compose -f compose.yaml -f compose.prod.yaml up -d --wait
```

## Deploying on Multiple Nodes

If you want to deploy your app on a cluster of machines, you can use [Docker Swarm](https://docs.docker.com/engine/swarm/stack-deploy/),
which is compatible with the provided Compose files.
To deploy on Kubernetes, take a look at [the Helm chart provided with API Platform](https://api-platform.com/docs/deployment/kubernetes/), which can be easily adapted for use with Symfony Docker.

## Passing local environment variables to containers

By default, `.env.local` and `.env.*.local` files are excluded from production images.
If you want to pass them to your containers, you can use the [`env_file` attribute](https://docs.docker.com/compose/environment-variables/set-environment-variables/#use-the-env_file-attribute):

```yaml
# compose.prod.yml

services:
    php:
        env_file:
            - .env.prod.local
        # ...
```
