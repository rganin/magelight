<?php


namespace Magelight\Webform\Models\Validation\Rules;

interface RuleInterface
{
    public function check($value, $arguments);

    public function getError();
}