<?php

namespace CommissionTask\Service\Commission;

use CommissionTask\Exceptions\OperationIsNotValidException;
use CommissionTask\Service\DataAdapter\Traits\HttpAdapter;
use CommissionTask\Service\Math;
use Exception;

class Operation
{
    public const WITHDRAW = 'withdraw';
    public const DEPOSIT = 'deposit';

    use HttpAdapter;

    /**
     * @var string[]
     */
    public static array $validOperations = [
        self::WITHDRAW,
        self::DEPOSIT,
    ];

    public const SCALE = 2;
    /**
     * @var array
     */
    private static array $operations = [];

    private static array $userWithDrawOperationsHistory = [];

    public static function getClient(int $clientId = null)
    {
        if($clientId === null) {
            return static::$operations;
        }else {
            return static::$operations[$clientId];
        }
    }

    public function getUserOperations(int $userId)
    {
        return static::$userWithDrawOperationsHistory[$userId];
    }

    public function __construct(array $attributes)
    {
        $this->id = count(static::$operations);
        $this->setOperationData($attributes);
        static::$operations[$this->id] = $this;
        if($this->operationType === self::WITHDRAW) {
            static::$userWithDrawOperationsHistory[$this->userId][] = $this;
        }
    }

    public static function getLog()
    {
        var_dump(static::$userWithDrawOperationsHistory);
    }

    /**
     * @param $attributes
     * @return void
     */
    public function setOperationData($attributes): void
    {
        $this->date = $attributes[0];
        $this->userId = $attributes[1];
        $this->userType = $attributes[2];
        $this->operationType = $attributes[3];
        $this->amount = $attributes[4];
        $this->currency = $attributes[5];
    }

    /**
     * @throws OperationIsNotValidException
     * @throws Exception
     */
    public function calculate()
    {
        $operationType = $this->operationType;
        if(!in_array($operationType , self::$validOperations)) {
            throw new OperationIsNotValidException();
        }

        echo number_format(
            (float)$this->getDepositOrWithdrawObject($operationType)
                ->calculateCommissionFee($this->amount),
            self::SCALE
            ) . "\n";
    }

    /**
     * @param mixed $operationType
     * @return Deposit|Withdraw
     */
    private function getDepositOrWithdrawObject(mixed $operationType): Deposit|Withdraw
    {
        if ($operationType === self::DEPOSIT) {
            $depositOrWithdrawObject = (new Deposit(new Math(self::SCALE)));
        } else {
            $depositOrWithdrawObject = (
                new Withdraw(
                    new Math(self::SCALE),
                    $this,
                    self::$userWithDrawOperationsHistory[$this->userId]
                )
            );
        }
        return $depositOrWithdrawObject;
    }
}