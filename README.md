# Felix Arntz MU Plugins

My collection of MU plugins in individual files within a subdirectory, fully configurable in a maintainable way which still allows you to apply updates from this repository. [Learn more about MU plugins.](https://developer.wordpress.org/advanced-administration/plugins/mu-plugins/)

1. [Context](#context)
2. [Project structure](#project-structure)
3. [Quick start](#quick-start)
4. [Alternative usage](#alternative-usage)
5. [License](#license)

## Context

I have been using MU plugins on my personal site for many years, and I thought some may find them useful as well. I am aware that several individuals and companies have open-sourced some of their MU plugins, but I wanted to go a step further than that by making them actually reusable beyond just my own specific site needs. So with this repository I came up with an approach that allows you to use any of my MU plugins in a way that allows you to customize them and even apply updates to them, without having to manually copy files every time.

## Project structure

At a high level, the project is made up by three components:
* The individual MU plugin files `felixarntz-mu-plugins/*.php`, where each file is for a single standalone feature.
* Some shared utility files `felixarntz-mu-plugins/shared/*.php`, which some of the individual MU plugin files are using.
* The overarching loader file `felixarntz-mu-plugins.php`, which loads and configures the individual MU plugin files that you would like to use.

## Quick start

There are several alternative ways to use this project, depending on your needs and flexibility. In any case, the project needs to be placed in your `wp-content/mu-plugins` directory. If your site doesn't have such a directory yet, you can simply create it.

If you use [Composer](https://getcomposer.org/) to manage your site's dependencies:
```
composer require felixarntz/felixarntz-mu-plugins
```

Otherwise, you can clone the repository:
```
git clone https://github.com/felixarntz/felixarntz-mu-plugins.git wp-content/mu-plugins/felixarntz-mu-plugins
```

Or, if you prefer to go fully manual, you can download a ZIP of the repository and extract it into your `wp-content/mu-plugins` directory.

After installing the project, you need to copy the `wp-content/mu-plugins/felixarntz-mu-plugins/felixarntz-mu-plugins.php` file one level up, i.e. to `wp-content/mu-plugins/felixarntz-mu-plugins.php`. Afterwards, you should edit that file to customize which features you want to load and to configure them, as you most certainly don't want to use all of them without tweaking their behavior to your site's needs. Please see [the class's inline documentation](felixarntz-mu-plugins.php) for how to make those modifications.

To apply updates to the project later, if you use Composer, a simple `composer update` will do it. Otherwise, you need to update the `wp-content/mu-plugins/felixarntz-mu-plugins` directory with the latest version, either via `git pull` from within the directory, or by downloading the latest ZIP and replacing the directory with its contents.

## Alternative usage

It is **recommended** that you use the approach from the quick start section above, where you install the project into its own directory within your `wp-content/mu-plugins` folder, and then copy the `felixarntz-mu-plugins.php` file from the project one level up. This has multiple benefits:
* You can apply updates the project at any point by simply replacing the directory with its newer version.
* You can customize the configuration via the copied `wp-content/mu-plugins/felixarntz-mu-plugins.php` file without losing these customizations when you update.
* (Optional) If you use [Composer](https://getcomposer.org/) to manage your site's dependencies, you can handle updates automatically by including the project in your `composer.json` file.

Alternatively, you can put the `felixarntz-mu-plugins` directory as well as the `felixarntz-mu-plugins.php` file from the project directly into your `wp-content/mu-plugins` folder. While this approach still allows you to update and customize, it makes the process a bit more complicated as you have to manually only replace the `felixarntz-mu-plugins` directory within the project so that you don't overwrite your customizations to the `felixarntz-mu-plugins.php` file. Last but not least, this approach doesn't work if you want to be able to update the project using Composer. So there are no benefits to this approach.

If you don't want to use the entire project, you could also pick individual files from within the `felixarntz-mu-plugins` directory, plus the `felixarntz-mu-plugins/shared` directory, and put them directly into your `wp-content/mu-plugins` folder. However, this approach is **not recommended**. While it may seem like a lightweight solution if you only need a few of the features, it really makes maintenance unnecessarily complicated without any benefit, unless you _never_ plan to update those files. The project only loads the files that you want to be loaded anyway, so the mere presence of files with features you don't need doesn't have any notable memory or performance impact.

## License

This plugin is free software, and is released under the terms of the GNU General Public License version 2 or (at your option) any later version. See [LICENSE](/LICENSE) for complete license.
