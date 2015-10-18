<?php
namespace SampleApp\Blocks;

use \Magelight\Webform\Blocks\Form as Form;
use \Magelight\Webform\Blocks\Fieldset as Fieldset;
use \Magelight\Webform\Blocks\Elements as Elements;

/**
 * @method static \SampleApp\Blocks\Forms forge()
 */
class Forms extends \Magelight\Block
{
    /**
     * Register form template
     *
     * @var string
     */
    protected $template = 'SampleApp/templates/forms.phtml';

    /**
     * Forgery constructor
     */
    public function __forge()
    {

    }

    /**
     * @return Form
     */
    public function getSampleForm()
    {
        $form = Form::forge()->setHorizontal()->setConfigs('sample', $this->url('sample/form'));

        $fieldset = Fieldset::forge();
        $fieldset->setLegend('Register new user');
        $fieldset->addRowField(Elements\Input::forge()->setName('name'), __('Name'));
        $fieldset->addRowField(Elements\Input::forge()->setName('email'), __('E-Mail'));
        $fieldset->addRowField(Elements\PasswordInput::forge()->setName('password'), __('Password'));
        $fieldset->addRowField(
            Elements\Captcha::forge(
                $this->url(\Magelight\Config::getInstance()->getConfigString('global/auth/urls/render_captcha_url'))
            )->setName('captcha')->addClass('col-md-6'),             __('Enter protection code')
        );
        return $form->addFieldset($fieldset)
            ->createResultRow(true)
            ->addButtonsRow(Elements\Button::forge()->setContent(__('Register'))->addClass('btn-primary'))
            ->loadFromRequest()->setValidator($this->getSampleFormValidator())->validateOnFront();
    }

    /**
     * Get form validator
     *
     * @return \Magelight\Webform\Models\Validator
     */
    public function getSampleFormValidator()
    {
        $validator = \Magelight\Webform\Models\Validator::forge();
        $validator->fieldRules('captcha')
            ->validatePermanent()->captcha()->setCustomError(__('Protection code is incorrect'));

        $validator->fieldRules('password', __('Password'))
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(32)->chainRule();

        $validator->fieldRules('passconf', __('Password confirmation'))->required()->chainRule()
            ->equals(\Magelight\Http\Request::getInstance()->getPost('regform')['password'], __('entered password'));

        $validator->fieldRules('name')
            ->required()->chainRule()
            ->minLength(3)->chainRule()
            ->maxLength(32)->chainRule()
            ->pregMatch('/[a-z0-9а-я]*/i');

        $validator->fieldRules('email')->required()->chainRule()->email();

        $validator->setErrorsLimit(1);
        return $validator;
    }
}