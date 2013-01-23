<?

namespace Magelight\Smtp;

/**
 * @property string $to
 * @property string $from
 * @property string $message
 * @property string $encoding
 */
class Mail
{
    use \Magelight\Traits\TForgery;
    use \Magelight\Traits\TGetSet;

    protected $_config = [
        'encoding' => 'utf-8',
    ];

    public function __forge($from, $to, $message = null)
    {
        $this->setGetSetTarget($this->_config);
        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
    }


}