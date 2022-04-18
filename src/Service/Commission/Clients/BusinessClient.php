<?php

namespace CommissionTask\Service\Commission\Clients;

use CommissionTask\Service\Commission\Contracts\CalculateCommissionInterface;
use CommissionTask\Service\Math;

class BusinessClient implements CalculateCommissionInterface
{
    /**
     * @var float
     */
    private float $commission = 0.005; // read from env

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
     * @param string $amount
     *
     * @return string
     */
    public function calculateCommissionFee(string $amount): string
    {
        return $this->math->multiply($amount, $this->commission);
    }
}