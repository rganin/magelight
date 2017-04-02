Magelight
=========

Magelight is a lightweight MVC framework inspired by [Magento](http://magentocommerce.com) written ground-up in PHP 5.4.

Magelight Features
---------

- Public and private code pools
- Object forgery with class redefinition mechanism
- Extended XML configuration
- Improved Blocks rendering mechanism without layout updates
- Simple logging and caching
- Improved routing markdown
- Query builder
- Bundled [jQuery](http://jquery.com)
- Bundled [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
- Form builder and validator
- And more...

Requirements
---------

- PHP 7+

Installation
---------

Installation with clone
------------------

To deploy the framework and application sample:
- clone to directory
- create an apache vhost (or nginx server) pointing to `app` directory
- add +r rights to `app/var`, `app/pub/static`
- copy app/etc/config.xml.dist to app/etc/config.xml and set your database preferences and directory options
- run `php -f upgrade.php` to create database and install schema and data updates
- you are done


Installation with composer
------------------
- Create `composer.json` file in your project root or add Magelight to your existing `composer.json`
```
{
  "name": "yourname/project",
  "description": "Your project",
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "rganin/magelight",
        "version" : "0.0.2",
        "source": {
          "url": "https://github.com/rganin/magelight",
          "type": "git",
          "reference": "0.0.2"
        },
        "autoload": {
          "files": ["core.php"]
        }
      }
    }
  ],
  "require": {
    "php": ">=7",
    "rganin/magelight": "*"
  }
}
```
- require `vendor/autoload.php` in your project
- create modules directory in your project or copy sample application structure `vendor/rganin/magelight/app/*`
  to your project directory
- do not forget to add your own modules directory by `\Magelight::app()->addModulesDir('modules/directory')` in your project
- run application with `\Magelight::app()->init()->run()`