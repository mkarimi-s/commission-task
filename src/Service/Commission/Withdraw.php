<?php

namespace CommissionTask\Service\Commission;

use CommissionTask\Service\Commission\Clients\BusinessClient;
use CommissionTask\Service\Commission\Clients\PrivateClient;
use CommissionTask\Service\Commission\Enum\Clients;
use CommissionTask\Service\Commission\Contracts\CalculateCommissionInterface;
use CommissionTask\Service\Math;

class Withdraw implements CalculateCommissionInterface
{
    /**
     * @var Math
     */
    private Math $math;

    /**
     * @param Math $math
     */
    public function __construct(Math $math)
    {
        $this->math = $math;
    }

    /**
     * @param string $type
     * @return BusinessClient|PrivateClient
     * @throws \Exception
     */
    public function getClientType(string $type): BusinessClient|PrivateClient
    {
        $math = new Math();
        return match ($type) {
            Clients::PRIVATE => new PrivateClient($math),
            Clients::BUSINESS => new BusinessClient($math),
            default => throw new \Exception('Client is not valid'),
        };
    }

    public function calculateCommissionFee(string $amount): string
    {
        return $this->getClientTye()->calculateCommissionFee();
    }
}