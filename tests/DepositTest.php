<?php

namespace Tests;

use CommissionTask\Service\Commission\Deposit;
use CommissionTask\Service\Math;
use PHPUnit\Framework\TestCase;

class DepositTest extends TestCase
{

    /**
     * @var float
     */
    private float $commission = 0.0003;

    /**
     * @var Math
     */
    private $math;

    /**
     * @var Deposit
     */
    private $deposit;

    /**
     * @var int 2
     */
    private $scale = 2;

    public function setUp(): void
    {
        $this->math = new Math($this->scale);
        $this->deposit = new Deposit($this->math);
    }

    /**
     * @param string $amount
     * @param string $expectation
     *
     * @dataProvider dataProviderForCalculateTesting
     */
    public function testCalculate(string $amount, string $expectation)
    {
        $this->assertEquals(
            number_format((float)$expectation, $this->scale),
            $this->deposit->calculateCommissionFee($amount, $this->commission)
        );
    }

    public function dataProviderForCalculateTesting(): array
    {
        return [
            'getting deposit commission' => ['200.00', '0.06'],
            'getting deposit commission' => ['400.00', '0.12'],
        ];
    }
}