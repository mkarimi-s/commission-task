<?php

namespace CommissionTask\Service;

use CommissionTask\Exceptions\CsvFileNotExistException;

class CSVReader
{
    public static string $fileExtension = 'csv';

    /**
     * @var array
     */
    private array $allCsvDataInArrayFormat;

    /**
     * @var string
     */
    public $fileAddress;

    public function __construct($fileAddress)
    {
        $this->fileAddress = $fileAddress;
    }

    /**
     * @throws CsvFileNotExistException
     */
    public function readLine(): void
    {
        $this->checkFile(function (): void {
            $file = fopen($this->fileAddress, 'r');
            while (($line = fgetcsv($file)) !== FALSE) {
                $csvData[] = $line;
            }
            fclose($file);

            $this->allCsvDataInArrayFormat = $csvData;
        });
    }

    /**
     * @throws CsvFileNotExistException
     */
    public function checkFile(callable $call)
    {
        if(!file_exists($this->fileAddress)) {
            throw new CsvFileNotExistException();
        }

        $fileInfo = pathinfo($this->fileAddress);
        if($fileInfo["extension"] != self::$fileExtension) {
            throw new CsvFileNotExistException();
        }

        call_user_func($call);
    }

    /**
     * @return array
     */
    public function getAllCsvDataInArrayFormat(): array
    {
        return $this->allCsvDataInArrayFormat;
    }
}