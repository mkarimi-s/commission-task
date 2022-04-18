<?php

namespace CommissionTask\Exceptions;

class CurrencyIsNotValidException extends \Exception
{
    protected $message = 'currency is not valid! please check csv file';

    protected $code = 404;
}