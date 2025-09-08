# Using Xdebug

The default development image is shipped with [Xdebug](https://xdebug.org/),
a popular debugger and profiler for PHP.

Because it has a significant performance overhead, the step-by-step debugger
is disabled by default.
It can be enabled by setting the `XDEBUG_MODE` environment variable to `debug`.

On Linux and Mac:

```console
XDEBUG_MODE=debug docker compose up --wait
```

On Windows:

```console
set XDEBUG_MODE=debug&& docker compose up --wait&set XDEBUG_MODE=
```

## Debugging with Xdebug and PhpStorm

First, [create a PHP debug remote server configuration](https://www.jetbrains.com/help/phpstorm/creating-a-php-debug-server-configuration.html):

1. In the `Settings/Preferences` dialog, go to `PHP | Servers`
2. Create a new server:
   - Name: `symfony` (or whatever you want to use for the variable `PHP_IDE_CONFIG`)
   - Host: `localhost` (or the one defined using the `SERVER_NAME` environment variable)
   - Port: `443`
   - Debugger: `Xdebug`
   - Check `Use path mappings`
   - Absolute path on the server: `/app`

You can now use the debugger!

1. In PhpStorm, open the `Run` menu and click on `Start Listening for PHP Debug Connections`
2. Add the `XDEBUG_SESSION=PHPSTORM` query parameter to the URL of
   the page you want to debug, or use [other available triggers](https://xdebug.org/docs/step_debug#activate_debugger)

   Alternatively, you can use [the **Xdebug extension**](https://xdebug.org/docs/step_debug#browser-extensions)
   for your preferred web browser.

3. On command line, we might need to tell PhpStorm which
   [path mapping configuration](https://www.jetbrains.com/help/phpstorm/zero-configuration-debugging-cli.html#configure-path-mappings)
   should be used, set the value of the PHP_IDE_CONFIG environment variable to
   `serverName=symfony`, where `symfony` is the name of the debug server configured
   higher.

   Example:

   ```console
   XDEBUG_SESSION=1 PHP_IDE_CONFIG="serverName=symfony" php bin/console ...
   ```

## Debugging with Xdebug and Visual Studio Code

1. Install necessary [PHP extension for Visual Studio Code](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode).
2. Add [debug configuration](https://code.visualstudio.com/docs/debugtest/debugging-configuration#_launch-configurations)
   into your `.vscode\launch.json` file.

   Example:

   ```json
   {
     "version": "0.2.0",
     "configurations": [
       {
         "name": "Listen for Xdebug",
         "type": "php",
         "request": "launch",
         "port": 9003,
         "pathMappings": {
           "/app": "${workspaceFolder}"
         }
       }
     ]
   }
   ```

3. Use [Run and Debug](https://code.visualstudio.com/docs/debugtest/debugging#_start-a-debugging-session)
   options and run `Listen for Xdebug` command to listen for upcoming connections
   with [the **Xdebug extension**](https://xdebug.org/docs/step_debug#browser-extensions)
   installed and active.

## Troubleshooting

Inspect the installation with the following command.
The Xdebug version should be displayed.

```console
$ docker compose exec php php --version

PHP ...
    with Xdebug v3.x.x ...
```
