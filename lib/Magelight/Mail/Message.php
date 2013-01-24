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
namespace Magelight\Mail;

/**
 * Email message class
 *
 * @method static \Magelight\Mail\Message forge()
 */
class Message
{
    /**
     * Use forgery
     */
    use \Magelight\Traits\TForgery;

    /**
     * New line constants
     */
    const CR    = "\r";
    const LF    = "\n";
    const CRLF  = "\r\n";

    /**
     * Message types constants
     */
    const TYPE_HTML = 'html';
    const TYPE_TEXT = 'text';

    /**
     * Line delimiter
     *
     * @var string
     */
    protected $_eol = self::CRLF;

    /**
     * Recepients
     *
     * @var array
     */
    protected $_to = [];

    /**
     * From address
     *
     * @var string
     */
    protected $_from = '';
    
    /**
     * Subject
     *
     * @var string
     */
    protected $_subject = '';

    /**
     * Plain text content
     *
     * @var string
     */
    protected $_textContent = '';

    /**
     * HTML message content
     *
     * @var string
     */
    protected $_htmlContent = '';
    
    /**
     * Complete body with mixed content
     *
     * @var string
     */
    protected $_body = '';

    /**
     * Array of attachments paths
     *
     * @var array
     */
    protected $_attachments = [];
    
    /**
     * Array of headers
     *
     * @var array
     */
    protected $_headers = [];

    /**
     * Compiled headers
     *
     * @var string
     */
    protected $_headerString = '';

    /**
     * Boundary hash
     *
     * @var string
     */
    protected $_boundaryHash;

    /**
     * Is mail sent flag
     *
     * @var boolean
     */
    protected $_isSent;

    /**
     * Charset
     *
     * @var string
     */
    protected $_charset = 'utf-8';

    /**
     * Reply to address
     *
     * @var string
     */
    protected $_replyTo = '';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->_attachments   = [];
        $this->_headers       = [];
        $this->_boundaryHash  = md5(date('r', time()));
    }

    /**
     * Set message subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject = '')
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Set message sender address
     *
     * @param string $from
     * @return Message
     */
    public function setFrom($from)
    {
        $this->_from = $from;
        return $this;
    }

    /**
     * Add recepient
     *
     * @param string $address
     * @param string $name
     * @return Message
     */
    public function addRecepient($address, $name = null)
    {
        $this->_to[] = empty($name) ? $address : "{$name} <{$address}>";
        return $this;
    }

    /**
     * Set message encoding (default is UTF8)
     *
     * @param string $encoding
     * @return Message
     */
    public function setEncoding($encoding = 'utf-8')
    {
        $this->_charset = $encoding;
        return $this;
    }

    /**
     * Set reply to address
     *
     * @param string $replyToAddress
     * @return Message
     */
    public function setReplyTo($replyToAddress)
    {
        $this->_replyTo = $replyToAddress;
        return $this;
    }

    /**
     * Set message content
     *
     * @param string $content
     * @param string $type
     *
     * @return Message
     */
    public function setContent($content, $type = self::TYPE_TEXT)
    {
        if ($type == self::TYPE_HTML) {
            $this->_htmlContent = $content;
        } else {
            $this->_textContent = $content;
        }
        return $this;
    }

    /**
     * Send message
     *
     * @return bool
     */
    public function send()
    {
        $this->_prepareHeaders();
        $this->_prepareBody();

        if (!empty($this->_attachments)) {
            $this->_prepareAttachments();
        }

        $this->_isSent = mail(implode(', ', $this->_to), $this->_subject, $this->_body, $this->_headerString);
        return $this->_isSent;
    }

    /**
     * Add message header
     *
     * @param $header
     */
    public function addHeader($header)
    {
        $this->_headers[] = $header;
    }

    /**
     * Add attachment
     *
     * @param string $file
     * @return Message
     */
    public function addAttachment($file)
    {
        $this->_attachments[] = $file;
        return $this;
    }

    /**
     * Prepare message body
     *
     * @return Message
     */
    protected function _prepareBody()
    {
        $this->_body .= "--PHP-mixed-{$this->_boundaryHash}" . $this->_eol;
        $this->_body .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-{$this->_boundaryHash}\""
            . $this->_eol
            . $this->_eol;
        if (!empty($this->_textContent)) {
            $this->_prepareText();
        }
        if (!empty($this->_htmlContent)) {
            $this->_prepareHtml();
        }
        $this->_body .= "--PHP-alt-{$this->_boundaryHash}--" . $this->_eol . $this->_eol;
        return $this;
    }

    /**
     * Prepare message headers
     *
     * @return Message
     */
    protected function _prepareHeaders()
    {
        $this->_setDefaultHeaders();
        $this->_headerString = implode($this->_eol, $this->_headers) . $this->_eol;
        return $this;
    }

    /**
     * Set default message headers
     *
     * @return Message
     */
    protected function _setDefaultHeaders()
    {
        $this->_headers[] = 'MIME-Version: 1.0';
        if (!empty($this->_from) && !empty($this->_replyTo)) {
            $this->_headers[] = 'Reply-To: ' . $this->_replyTo . $this->_eol;
            $this->_headers[] = 'Return-Path: ' . $this->_from . $this->_eol;
        } else {
            $this->_headers[] = 'From: ' . $this->_from;
        }
        $this->_headers[] = 'To: ' . implode(', ', $this->_to);
        $this->_headers[] = 'Subject: ' . $this->_subject;
        // We'll assume a multi-part message so that we can include an HTML and a text version of the email at the
        // very least. If there are attachments, we'll be doing the same thing.
        $this->_headers[] = "Content-type: multipart/mixed; boundary=\"PHP-mixed-{$this->_boundaryHash}\"";
        return $this;
    }

    /**
     * Prepare attachments
     *
     * @return Message
     */
    protected function _prepareAttachments()
    {
        foreach ($this->_attachments as $attachment) {

            $file_name  = basename($attachment);
            $this->_body .= "--PHP-mixed-{$this->_boundaryHash}" . $this->_eol;
            $this->_body .= "Content-Type: application/octet-stream; name=\"{$file_name}\"" . $this->_eol;
            $this->_body .= 'Content-Transfer-Encoding: base64' . $this->_eol;
            $this->_body .= 'Content-Disposition: attachment' . $this->_eol;
            $this->_body .= chunk_split(base64_encode(file_get_contents($attachment)));
            $this->_body .= $this->_eol . $this->_eol;

        }

        $this->_body .= "--PHP-mixed-{$this->_boundaryHash}--" . $this->_eol;
        return $this;
    }

    /**
     * Prepare message text
     *
     * @return Message
     */
    protected function _prepareText()
    {
        $this->_body .= "--PHP-alt-{$this->_boundaryHash}" . $this->_eol;
        $this->_body .= "Content-Type: text/plain; charset=\"{$this->_charset}\"" . $this->_eol;
        $this->_body .= 'Content-Transfer-Encoding: 7bit' . $this->_eol;
        $this->_body .= $this->_textContent . $this->_eol . $this->_eol;
        return $this;
    }

    /**
     * Prepare HTML body
     *
     * @return Message
     */
    protected function _prepareHtml()
    {
        $this->_body .= "--PHP-alt-{$this->_boundaryHash}" . $this->_eol;
        $this->_body .= "Content-Type: text/html; charset=\"{$this->_charset}\"" . $this->_eol;
        $this->_body .= 'Content-Transfer-Encoding: 7bit' . $this->_eol . $this->_eol;
        $this->_body .= $this->_htmlContent . $this->_eol . $this->_eol;
        return $this;
    }
}
