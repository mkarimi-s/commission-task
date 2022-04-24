<?php

namespace CommissionTask\Service\Commission;

use CommissionTask\Service\Commission\Clients\BusinessClient;
use CommissionTask\Service\Commission\Clients\PrivateClient;
use CommissionTask\Service\Commission\Enum\Clients;
use CommissionTask\Service\Commission\Contracts\CalculateCommissionInterface;
use CommissionTask\Service\Math;
use Exception;

class Withdraw implements CalculateCommissionInterface
{
    /**
     * @var Math
     */
    private Math $math;

    /**
     * @var Operation
     */
    private Operation $operation;

    /**
     * @var array
     */
    private array $userWithdrawOperationsHistory;

    /**
     * @param Math $math
     * @param Operation $operation
     * @param array $userWithdrawOperationsHistory
     */
    public function __construct(Math $math, Operation $operation, array $userWithdrawOperationsHistory)
    {
        $this->math = $math;
        $this->operation = $operation;
        $this->userWithdrawOperationsHistory = $userWithdrawOperationsHistory;
    }

    /**
     * @return BusinessClient|PrivateClient
     * @throws Exception
     */
    public function getClientType(): BusinessClient|PrivateClient
    {
        return match ($this->operation->userType) {
            Clients::PRIVATE => new PrivateClient($this->math, $this->operation, $this->userWithdrawOperationsHistory),
            Clients::BUSINESS => new BusinessClient($this->math),
            default => throw new Exception('Client is not valid'),
        };
    }

    /**
     * @throws Exception
     */
    public function calculateCommissionFee(string $amount): string
    {
        return ($this->getClientType())->calculateCommissionFee($this->operation->amount);
    }
}