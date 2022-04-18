<?php

namespace CommissionTask\Exceptions;

class CsvFileNotExistException extends \Exception
{
    protected $message = 'csv file is not exists or valid';

    protected $code = 404;
}