To deploy this application sample:
- move it to the directory you like
- create an apache vhost (or nginx server) pointing to this directory
- copy app/etc/config.xml.dist to app/etc/config.xml and set your database preferences and directory options
- create a symlink to Magelight private modules code pool
    Windows$  mklink /D modules\private X:\{your-magelight-path}\modules\private
    Unix$ ln -s /var/{you-magelight-path}/modules/private modules/private
- change `var` directory rights
    Unix$ chmod -R 0755 var && chown -R {your-www-user}:{your-www-group} var
- open frontend