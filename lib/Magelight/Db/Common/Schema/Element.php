<?php
/**
 * Created by PhpStorm.
 * User: rganin
 * Date: 9/24/18
 * Time: 10:43 PM
 */

abstract class Element
{
    protected $name;

    protected $type;

    /**
     * Get element name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set element name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
}
