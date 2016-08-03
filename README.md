# InstaCraft

An automatic instagram image puller for <a href="https://craftcms.com/" target="_blank">Craft CMS</a> without OAuth. You can download the images from a public instagram without OAuth. Or just insert a url and folderid in the configuration of your plugin and setup a scheduled task and it will automatically pull the images for you.

# Installation

## Composer

You can install this plugin with composer. This will install your plugin automatically in the craft plugins folder.

```composer require brammittendorff/instacraft```

## Git

You can install this plugin with git to you just need to clone this repo into your craft plugins folder:

```cd craft/plugins/```

```git clone https://github.com/brammittendorff/instacraft.git```


# Scheduled tasks

You can use this plugin as a cronjob, or as a schedule in heroku just use/run the following command:

```./craft/app/etc/console/yiic instacraft run```
