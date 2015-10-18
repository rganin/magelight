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
 * @copyright Copyright (c) 2012-2015 rganin (rganin@gmail.com)
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
    protected $eol = self::CRLF;

    /**
     * Recepients
     *
     * @var array
     */
    protected $to = [];

    /**
     * From address
     *
     * @var string
     */
    protected $from = '';
    
    /**
     * Subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * Plain text content
     *
     * @var string
     */
    protected $textContent = '';

    /**
     * HTML message content
     *
     * @var string
     */
    protected $htmlContent = '';
    
    /**
     * Complete body with mixed content
     *
     * @var string
     */
    protected $body = '';

    /**
     * Array of attachments paths
     *
     * @var array
     */
    protected $attachments = [];
    
    /**
     * Array of headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Compiled headers
     *
     * @var string
     */
    protected $headerString = '';

    /**
     * Boundary hash
     *
     * @var string
     */
    protected $boundaryHash;

    /**
     * Is mail sent flag
     *
     * @var boolean
     */
    protected $isSent;

    /**
     * Charset
     *
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * Reply to address
     *
     * @var string
     */
    protected $replyTo = '';

    /**
     * Forgery constructor
     */
    public function __forge()
    {
        $this->attachments   = [];
        $this->headers       = [];
        $this->boundaryHash  = md5(date('r', time()));
    }

    /**
     * Set message subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject = '')
    {
        $this->subject = $subject;
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
        $this->from = $from;
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
        $this->to[] = empty($name) ? $address : "{$name} <{$address}>";
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
        $this->charset = $encoding;
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
        $this->replyTo = $replyToAddress;
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
            $this->htmlContent = $content;
        } else {
            $this->textContent = $content;
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
        $this->prepareHeaders();
        $this->prepareBody();

        if (!empty($this->attachments)) {
            $this->prepareAttachments();
        }

        $this->isSent = mail(implode(', ', $this->to), $this->subject, $this->body, $this->headerString);
        return $this->isSent;
    }

    /**
     * Add message header
     *
     * @param $header
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * Add attachment
     *
     * @param string $file
     * @return Message
     */
    public function addAttachment($file)
    {
        $this->attachments[] = $file;
        return $this;
    }

    /**
     * Prepare message body
     *
     * @return Message
     */
    protected function prepareBody()
    {
        $this->body .= "--PHP-mixed-{$this->boundaryHash}" . $this->eol;
        $this->body .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-{$this->boundaryHash}\""
            . $this->eol
            . $this->eol;
        if (!empty($this->textContent)) {
            $this->prepareText();
        }
        if (!empty($this->htmlContent)) {
            $this->prepareHtml();
        }
        $this->body .= "--PHP-alt-{$this->boundaryHash}--" . $this->eol . $this->eol;
        return $this;
    }

    /**
     * Prepare message headers
     *
     * @return Message
     */
    protected function prepareHeaders()
    {
        $this->setDefaultHeaders();
        $this->headerString = implode($this->eol, $this->headers) . $this->eol;
        return $this;
    }

    /**
     * Set default message headers
     *
     * @return Message
     */
    protected function setDefaultHeaders()
    {
        $this->headers[] = 'MIME-Version: 1.0';
        if (!empty($this->from) && !empty($this->replyTo)) {
            $this->headers[] = 'Reply-To: ' . $this->replyTo . $this->eol;
            $this->headers[] = 'Return-Path: ' . $this->from . $this->eol;
        } else {
            $this->headers[] = 'From: ' . $this->from;
        }
        $this->headers[] = 'To: ' . implode(', ', $this->to);
        $this->headers[] = 'Subject: ' . $this->subject;
        // We'll assume a multi-part message so that we can include an HTML and a text version of the email at the
        // very least. If there are attachments, we'll be doing the same thing.
        $this->headers[] = "Content-type: multipart/mixed; boundary=\"PHP-mixed-{$this->boundaryHash}\"";
        return $this;
    }

    /**
     * Prepare attachments
     *
     * @return Message
     */
    protected function prepareAttachments()
    {
        foreach ($this->attachments as $attachment) {

            $file_name  = basename($attachment);
            $this->body .= "--PHP-mixed-{$this->boundaryHash}" . $this->eol;
            $this->body .= "Content-Type: application/octet-stream; name=\"{$file_name}\"" . $this->eol;
            $this->body .= 'Content-Transfer-Encoding: base64' . $this->eol;
            $this->body .= 'Content-Disposition: attachment' . $this->eol;
            $this->body .= chunk_split(base64_encode(file_get_contents($attachment)));
            $this->body .= $this->eol . $this->eol;

        }

        $this->body .= "--PHP-mixed-{$this->boundaryHash}--" . $this->eol;
        return $this;
    }

    /**
     * Prepare message text
     *
     * @return Message
     */
    protected function prepareText()
    {
        $this->body .= "--PHP-alt-{$this->boundaryHash}" . $this->eol;
        $this->body .= "Content-Type: text/plain; charset=\"{$this->charset}\"" . $this->eol;
        $this->body .= 'Content-Transfer-Encoding: 7bit' . $this->eol;
        $this->body .= $this->textContent . $this->eol . $this->eol;
        return $this;
    }

    /**
     * Prepare HTML body
     *
     * @return Message
     */
    protected function prepareHtml()
    {
        $this->body .= "--PHP-alt-{$this->boundaryHash}" . $this->eol;
        $this->body .= "Content-Type: text/html; charset=\"{$this->charset}\"" . $this->eol;
        $this->body .= 'Content-Transfer-Encoding: 7bit' . $this->eol . $this->eol;
        $this->body .= $this->htmlContent . $this->eol . $this->eol;
        return $this;
    }
}
