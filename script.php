<?php

declare(strict_types=1);

require ('vendor/autoload.php');

use CommissionTask\Exceptions\CsvFileNotExistException;
use CommissionTask\Exceptions\CurrencyIsNotValidException;
use CommissionTask\Exceptions\OperationIsNotValidException;
use CommissionTask\Service\Commission\Operation;
use CommissionTask\Service\CSVReader;
use CommissionTask\Service\DataAdapter\Traits\HttpAdapter;

class App {

    use HttpAdapter;
    public static stdClass $exchangeRates;

    /**
     * @throws CsvFileNotExistException
     * @throws OperationIsNotValidException
     * @throws CurrencyIsNotValidException
     */
    public function run(): void
    {
        $this->getExchangeRates();
        $this->readCsv();
    }

    public function getExchangeRates()
    {
        self::$exchangeRates = $this->makeRequest('get', 'tasks/api/currency-exchange-rates');
    }

    /**
     * @return void
     *
     * @throws CsvFileNotExistException
     * @throws CurrencyIsNotValidException
     * @throws OperationIsNotValidException
     */
    private function readCsv(): void
    {
        $csvReader = (new CSVReader('input.csv'));
        $csvReader->readLine();
        $data = $csvReader->getAllCsvDataInArrayFormat();

        foreach ($data as $row) {
            $operation = new Operation($row);
            $operation->calculate();
        }
    }
}

(new App())->run();
