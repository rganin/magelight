<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 15.12.12
 * Time: 23:20
 * To change this template use File | Settings | File Templates.
 */

$path_prefix = 'current/';

$settings = [
    'files' => [
        'bootstrap.php.dist.phtml' => [
            'destination' => 'bootstrap.php',
            'overwrite' => true,
            'params' => [
                'magelight_dir' => '/var/magelight',
            ],
        ],
        'etc/config.xml.dist.phtml' => [
            'destination' => 'etc/config.xml',
            'overwrite'  => true,
            'params' => [
            'recaptcha_private_key'=> '6LdvcNoSAAAAAD-8fohX-vRBwIn-TKZJioqtzzIw',
            'recaptcha_public_key' => '6LdvcNoSAAAAAMnWdVMjWghfwBTSOxjjpGv54yuk',
                'cache_modules_config' => '1',
                'developer_mode'       => '0',
                'db_default_host'      => '127.0.0.1',
                'db_default_port'      => '3306',
                'db_default_user'      => 'app_db',
                'db_default_password'  => '',
                'db_default_dbname'    => 'app_db',
                'db_default_profiling' => '0',
                'log_file'             => 'var/app.log',
                'view_cache_ttl'       => '3600',
                'base_domain'          => 'host'
            ],
        ]
    ]
];

set_error_handler('handle_error');

foreach ($settings['files'] as $template => $file) {
    if (file_exists($path_prefix . $file['destination']) && (!isset($file['overwrite']) || !$file['overwrite'])) {
        continue;
    }
    $block = new Block($file['params']);
    $block->setTemplate($path_prefix . $template);
    $html = $block->toHtml();
    file_put_contents($path_prefix . $file['destination'], $html);
}

function handle_error($errno, $errstr, $errfile, $errline)
{
    echo $errstr;
    die();
}

class Block
{
    /**
     * Path to template
     *
     * @var string
     */
    protected $_template = null;

    /**
     * Blocks variables
     *
     * @var array
     */
    protected $_vars = [];

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct($params)
    {
        $this->_vars = $params;
    }

    /**
     * Set Blocks property
     *
     * @param string $name
     * @param mixed $value
     * @return Block
     */
    public function set($name, $value)
    {
        $this->_vars[$name] = $value;
        return $this;
    }
    /**
     * Get Blocks property
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : $default;
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_vars[$name] = $value;
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
    }

    /**
     * Isset magic
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_vars[$name]);
    }

    /**
     * Render Blocks to html or whatever is given in template
     *
     * @return string
     * @throws Exception
     */
    public function toHtml()
    {
        ob_start();
        include($this->_template);
        $html = ob_get_clean();
        return $html;
    }
    /**
     * Test template
     *
     * @param string $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }
}
