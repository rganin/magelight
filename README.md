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

- PHP 5.4.7+

Installation
---------

To deploy the framework and application sample:
- move it to the directory you like
- create an apache vhost (or nginx server) pointing to this directory
- copy app/etc/config.xml.dist to app/etc/config.xml and set your database preferences and directory options
- create a symlink to Magelight private modules code pool
-- Windows$  mklink /D modules\private X:\{your-magelight-path}\modules\private
-- Unix$ ln -s /var/{you-magelight-path}/modules/private modules/private
   or just copy framework `modules/private` directory to app/modules/
- change `var` directory rights
-- Unix$ chmod -R 0755 var && chown -R {your-www-user}:{your-www-group} var
- open frontend