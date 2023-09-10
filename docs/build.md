# Build Options

## Customizing the Server Name

Use the `SERVER_NAME` environment variable to define your custom server name(s).

    SERVER_NAME="app.localhost" docker compose up --wait

*Tips: You can define your server name variable in your `.env` file to keep it at each up*

## Using custom HTTP ports

Use the environment variables `HTTP_PORT`, `HTTPS_PORT` and/or `HTTP3_PORT` to adjust the ports to your needs, e.g.

    HTTP_PORT=8000 HTTPS_PORT=4443 HTTP3_PORT=4443 docker compose up --wait

to access your application on [https://localhost:4443](https://localhost:4443).

*Note: Let's Encrypt only supports the standard HTTP and HTTPS ports. Creating a Let's Encrypt certificate for another port will not work, you have to use the standard ports or to configure Caddy to use another provider.*
