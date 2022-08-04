# Using Xdebug

The default development image is shipped with [Xdebug](https://xdebug.org/),
a popular debugger and profiler for PHP.

Because it has a significant performance overhead, the step-by-step debugger is disabled by default.
It can be enabled by setting the `XDEBUG_MODE` environment variable to `debug`.

On Linux and Mac:

```
XDEBUG_MODE=debug docker compose up -d
```

On Windows:

```
set XDEBUG_MODE=debug&& docker compose up -d&set XDEBUG_MODE=
```

## Debugging with Xdebug and PHPStorm

First, [create a PHP debug remote server configuration](https://www.jetbrains.com/help/phpstorm/creating-a-php-debug-server-configuration.html):

1. In the `Settings/Preferences` dialog, go to `PHP | Servers`
2. Create a new server:
   * Host: `localhost` (or the one defined using the `SERVER_NAME` environment variable)
   * Port: `443`
   * Debugger: `Xdebug`
   * Check `Use path mappings`
   * Absolute path on the server: `/srv/app`

You can now use the debugger!

1. In PHPStorm, open the `Run` menu and click on `Start Listening for PHP Debug Connections`
2. Add the `XDEBUG_SESSION=PHPSTORM` query parameter to the URL of the page you want to debug, or use [other available triggers](https://xdebug.org/docs/step_debug#activate_debugger)

Alternatively, you can use [the **Xdebug extension**](https://xdebug.org/docs/step_debug#browser-extensions) for your preferred web browser. 

## Troubleshooting

Inspect the installation with the following command. The Xdebug version should be displayed.

```console
$ docker compose exec php php --version

PHP ...
    with Xdebug v3.1.2 ...
```
