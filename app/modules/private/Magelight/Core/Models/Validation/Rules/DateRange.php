<?php

namespace Magelight\Core\Models\Validation\Rules;

class DateRange extends AbstractRule
{

    protected $_error = 'Field %1$s must be a valid date between %1$s and %2$s';

    public function check($value, $args)
    {
        $dateRule = new DateAndTime();

        if (!$dateRule->check($value, $args)) {
            return false;
        } else {
            $ret = true;

            $a = strtotime($value);

            if (isset($args[0])) {
                if (!$dateRule->check($args[0], $args)) {
                    return false;
                }
                $ret &= ($a >= strtotime($args[0]));
            }

            if (isset($args[1])) {
                if (!$dateRule->check($args[1], $args)) {
                    return false;
                }
                $ret &= ($a <= strtotime($args[0]));
            }

            return $ret;
        }
    }
}