# VIC.GOV.AU
Drupal 8 implementation of VIC.GOV.AU

[![CircleCI](https://circleci.com/gh/dpc-sdp/vic-gov-au.svg?style=svg&circle-token=619001ceda795d221a96315242e2782f621612d4)](https://circleci.com/gh/dpc-sdp/vic-gov-au)

## Install Docker
1   - Install [Homebrew](https://brew.sh/)
   ```bash
   /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
   ```
    - Install docker `brew cask install docker`
    (You can also install it manually if you prefer - https://www.docker.com/docker-mac)
2. Start docker and you should be able to run `docker ps`

## Local environment setup
1. Checkout project repo and confirm the path is in docker's file sharing config - https://docs.docker.com/docker-for-mac/#file-sharing
2. Make sure that `composer` & `pygmy` are installed
   - Install pygmy `gem install pygmy` (you might need sudo for this depending on your ruby configuration)
   - Run `brew bundle` or install each package manually:
      - Install composer `brew install composer`
3. Make sure you don't have anything running on port 80 on the host machine (like a web server) then run `pygmy up` 
5. Run `composer build`
5. Once build has completed, you can run `composer login` to `drush uli` into the local site.

* If any steps fail you're safe to rerun from any point, 
starting again from the beginning will just reconfirm the changes.

Local URL -- http://content-vicgovau.docker.amazee.io/

## Running drush commands.

You'll need to connect to the CLI container to run any drush commands.

`docker-compose exec cli bash`

From here you can run any commands against the local environment.
We're in the process of implementing drush alias support so this is only temporary.

## Available `composer` commands
- `composer up` - bring up the project.
- `composer build` - bring up and build out branch.
- `composer db-import` - import prod db, run updates and config import.
- `composer test` - run tests.
- `composer login` - run drush uli on the CLI container.
- `composer logs` - get logs from running containers.
- `composer stop` - stop project containers.
- `composer destroy` - stop and remove all project containers.
- `composer cleanup` - remove all dependencies.
- `composer rebuild` - remove all dependencies and run `build`.
- `composer doctor` - helps to find the cause of any issues with a local setup.

## Logs.

Using the composer helper script you can get logs from any running container.

`composer logs`

You can also filter the output to show only logs from a particular service.
For example `composer logs -- php` will show the log output from the php container.
The full list of services can be found in the `docker-compose.yml`

## Mailhog.

Mailhog is included with pygmy and is available @ http://mailhog.docker.amazee.io/
Documentation for mailhog is available of the project page -- https://github.com/mailhog/MailHog

## Stage file proxy.

Stage File Proxy is enabled on all non production environments so files are automatically downloaded directly from prod on demand.

## Adding Drupal modules
`composer require drupal/module_name`
or for specific versions
`composer require drupal/module_name:1.2`

## Adding patches for drupal modules
1. Add `title` and `url` to patch on drupal.org to the `patches` array in `extra` section in `composer.json`.

```
    "extra": {
        "patches": {
            "drupal/core": {
                "Contextual links should not be added inside another link - https://www.drupal.org/node/2898875": "https://www.drupal.org/files/issues/contextual_links_should-2898875-3.patch"
            }
        }    
    }
```

2. `composer update --lock`

## Coding standards
PHP and JS code linting uses [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with Drupal rules from [Coder](https://www.drupal.org/project/coder) module and additional local overrides in `phpcs.xml.dist` and `.eslintrc`.   

## Behat tests
Behat configuration uses multiple extensions: 
- [Drupal Behat Extension](https://github.com/jhedstrom/drupalextension) - Drupal integration layer. Allows to work with Drupal API from within step definitions.
- [Behat Screenshot Extension](https://github.com/integratedexperts/behat-screenshot) - Behat extension and a step definition to create HTML and image screenshots on demand or test fail.
- [Behat Progress Fail Output Extension](https://github.com/integratedexperts/behat-format-progress-fail) - Behat output formatter to show progress as TAP and fail messages inline. Useful to get feedback about failed tests while continuing test run.
- `VicgovauDrupalContext` - Site-specific Drupal context with custom step definitions.

### Run tests locally:
- Run all tests: `composer test`
- Run specific test feature: `composer cli -- vendor/bin/behat --format=progress_fail --colors tests/behat/features/homepage.feature`

Read more information in [the wiki page](https://digital-engagement.atlassian.net/wiki/spaces/SDP/pages/134906009/Behat+testing).

## Automated builds (Continuous Integration)
In software engineering, continuous integration (CI) is the practice of merging all developer working copies to a shared mainline several times a day. 
Before feature changes can be merged into a shared mainline, a complete build must run and pass all tests on CI server.

This project uses [Circle CI](https://circleci.com/) as CI server: it imports production backups into fully built codebase and runs code linting and tests. When tests pass, a deployment process is triggered for nominated branches (usually, `master` and `develop`).

Add [skip ci] to the commit subject to skip CI build. Useful for documentation changes.

### SSH
Circle CI supports SSHing into the build for 30 minutes after the build is finished. SSH can be enabled either during the build run or when the build is started with SSH support.

### Cache
Circle CI supports caching between builds. The cache takes care of saving the state of your dependencies between builds, therefore making the builds run faster.
Each branch of your project will have a separate cache. If it is the very first build for a branch, the cache from the default branch on GitHub (normally `master`) will be used. If there is no cache for master, the cache from other branches will be used.
If the build has inconsistent results (build fails in CI but passes locally), try to re-running the build without cache by clicking 'Rebuild without cache' button.

### Test artifacts
Test artifacts (screenshots etc.) are available under 'Artifacts' tab in Circle CI UI.

### Debugging
1. Make sure `scripts/xdebug.sh` is executable.
2. Trigger xDebug from web browser so that PHPStorm recognises the server `content-vicgovau.docker.amazee.io` and configures the path mapping. Alternatively, you can create the server in PHPStorm Settings.
    * Make sure `serverName` to be `content-vicgovau.docker.amazee.io`
3. To debug `drush` commands:
    * SSH to CLI container: `docker-compose exec cli bash`
        + `cd docroot`
        + `../scripts/xdebug.sh ../vendor/bin/drush <DRUSH_COMMAND>`
            - Example: `../scripts/xdebug.sh ../vendor/bin/drush updb -y`
    * Debug directly from host machine: `composer debug-drush -- <DRUSH_COMMAND>`
        + Example: `composer debug-drush -- updb -y`
4. To connect to the local database, connect to 127.0.0.1 on port 13306.  

### Pre deployment database backups

An automatic backup is made of the production database before any new deployment.
This is currently stored in the private files directory and overridden on each deployment.

#### Restoring a backup

1. Access production `cli` container on bay.
2. Take a backup of failed deployment db for debugging.
  `drush sql-dump --gzip --result-file=/app/docroot/sites/default/files/private/failed_deployment.sql`
3. Import pre-deployment backup.
  `gzip -cd /app/docroot/sites/default/files/private/pre_deploy_backup.sql.gz | drush sqlc`
4. Clear cache.
  `drush cr`
