# Update your project

You-ve started a project with symfony-docker template and you'd like to benefit from the latest enhancements (i.e. FrankenPHP). Juste use this git based tool
[template-sync](https://github.com/mano-lis/template-sync) by running :
```shell
curl -sSL https://raw.githubusercontent.com/mano-lis/template-sync/main/template-sync.sh | sh -s -- [<url-of-the-template>](https://github.com/dunglas/symfony-docker)https://github.com/dunglas/symfony-docker
```
Resolve potential conflicts, run `git cherry-pick --continue` and job is done.

Full documentation [here](https://github.com/mano-lis/template-sync/blob/main/README.md)
