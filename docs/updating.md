# Updating Your Project

To import the changes made to the *Symfony Docker* template into your project, we recommend using
[*template-sync*](https://github.com/coopTilleuls/template-sync):

1. Run the script to synchronize your project with the latest version of the skeleton:

    ```console
    curl -sSL https://raw.githubusercontent.com/coopTilleuls/template-sync/main/template-sync.sh | sh -s -- https://github.com/dunglas/symfony-docker
    ```

2. Resolve conflicts, if any
3. Run `git cherry-pick --continue`

For more advanced options, refer to [the documentation of *template sync*](https://github.com/coopTilleuls/template-sync#template-sync).
