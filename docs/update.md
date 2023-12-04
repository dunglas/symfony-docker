# Update Your Project

You have started a project with the Symfony Docker template and you would like to benefit from the latest enhancements introduced since you created your project (i.e. FrankenPHP). Juste use this git based tool
[The *template-sync* project](https://github.com/mano-lis/template-sync) got you covered.

Run the following command to import the changes since your last update:

```console
curl -sSL https://raw.githubusercontent.com/mano-lis/template-sync/main/template-sync.sh | sh -s -- https://github.com/dunglas/symfony-docker
```
Resolve potential conflicts, run `git cherry-pick --continue` and you are done!

[Full documentation](https://github.com/mano-lis/template-sync/blob/main/README.md)
