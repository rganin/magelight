<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 03.01.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Webform\Blocks;

/**
 * @method static \Magelight\Webform\Blocks\FilterForm forge()
 */
class FilterForm extends Form
{
    protected $_useSession = true;

    /**
     * Load filter form from request
     *
     * @param \Magelight\Http\Request $request
     * @return FilterForm
     */
    public function loadFromRequest(\Magelight\Http\Request $request = null)
    {
        parent::loadFromRequest($request);
        if ($this->_useSession && empty($this->_requestFields)) {
            $data = \Magelight::app()->session()->get('filter_form_' . $this->getAttribute('name'));
            if (!empty($data)) {
                $this->_requestFields = $data;
            }
        }
        \Magelight::app()->session()->set('filter_form_' . $this->getAttribute('name'), $this->getRequestFields());
        return $this;
    }

    /**
     * Set use session for data storage
     *
     * @param bool $flag
     * @return FilterForm
     */
    public function useSession($flag = true)
    {
        $this->_useSession = $flag;
        return $this;
    }

    /**
     * Get collection filter from form
     *
     * @return mixed
     */
    public function getCollectionFilter()
    {
        return \Magelight\Db\CollectionFilter::forge($this->getRequestFields());
    }
}