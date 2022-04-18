<?php

namespace CommissionTask\Service\DataAdapter\Validator;

use RuntimeException;

class MethodCheckValidator
{
    const METHODS = ['get', 'post', 'put', 'patch', 'delete'];

    public function validate(string $method) :void
    {
        $check = in_array(strtolower($method), self::METHODS);
        if (!$check) {
            throw new RuntimeException($method . " method is not provided in http adapter", 503);
        }
    }
}
