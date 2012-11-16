<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2012 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Http;

class Response
{
    /**
     * Response headers
     *
     * @var array
     */
    protected $_headers = [];

    /**
     * Response content
     *
     * @var null|string
     */
    protected $_content = null;

    /**
     * Add header to response
     *
     * @param null $name
     * @param null $value
     * @return Response
     */
    public function addHeader($name = null, $value = null)
    {
        $this->_headers[] = array('name' => $name, 'value' => $value);
        return $this;
    }

    /**
     * Set response content
     *
     * @param null $content
     * @return Response
     */
    public function setContent($content = null)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Send response to client
     */
    public function send()
    {
        foreach ($this->_headers as $header) {
            $headerStr = '';
            if (!empty($header['name'])) {
                $headerStr .= $header['name'];
            }
            if (!empty($header['value'])) {
                $headerStr .=  ': ' . $header['value'];
            }
            header($headerStr);
        }
        echo $this->_content;
    }
}
