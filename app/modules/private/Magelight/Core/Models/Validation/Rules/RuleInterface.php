<?php


namespace Magelight\Core\Models\Validation\Rules;

interface RuleInterface
{
    public function check($value, $arguments);

    public function getError();
}