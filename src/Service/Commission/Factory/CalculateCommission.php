<?php

namespace CommissionTask\Service\Commission\Factory;

use CommissionTask\Service\Commission\Deposit;
use CommissionTask\Service\Commission\Enum\Operations;
use CommissionTask\Service\Commission\Withdraw;
use CommissionTask\Service\Math;

class CalculateCommission
{

    /**
     * @param string $type
     * @return Withdraw|Deposit
     * @throws \Exception
     */
    public function strategy(string $type): Withdraw|Deposit
    {
        $math = new Math();
        return match ($type) {
            Operations::WITHDRAW => new Withdraw($math),
            Operations::DEPOSIT => new Deposit($math),
            default => throw new \Exception('Operation is not valid'),
        };
    }
}