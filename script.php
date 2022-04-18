<?php

declare(strict_types=1);

require ('vendor/autoload.php');

use CommissionTask\Service\Commission\Operation;
use CommissionTask\Service\CSVReader;
use CommissionTask\Service\DataAdapter\Traits\HttpAdapter;

class App {

    use HttpAdapter;
    public static stdClass $exchangeRates;

    public function run()
    {
        $this->getExchangeRates();

        $csvReader =  (new CSVReader('input.csv'));
        $csvReader->readLine();
        $data = $csvReader->getAllCsvDataInArrayFormat();

        foreach($data as $row) {
            $operation = new Operation($row);
            $operation->calculate();
        }
    }

    public function getExchangeRates()
    {
        self::$exchangeRates = $this->makeRequest('get', 'tasks/api/currency-exchange-rates');
    }
}

(new App())->run();
