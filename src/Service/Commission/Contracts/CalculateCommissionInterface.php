<?php
namespace CommissionTask\Service\Commission\Contracts;

interface CalculateCommissionInterface {
    public function calculateCommissionFee(string $amount): string;
}