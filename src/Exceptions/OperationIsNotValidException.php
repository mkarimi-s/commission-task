<?php

namespace CommissionTask\Exceptions;

class OperationIsNotValidException extends \Exception
{
    protected $message = 'operation is not valid';

    protected $code = 403;
}