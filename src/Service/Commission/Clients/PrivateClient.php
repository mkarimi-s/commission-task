<?php

namespace CommissionTask\Service\Commission\Clients;

use App;
use CommissionTask\Exceptions\CurrencyIsNotValidException;
use CommissionTask\Service\Commission\Contracts\CalculateCommissionInterface;
use CommissionTask\Service\Commission\Operation;
use CommissionTask\Service\DataAdapter\Traits\HttpAdapter;
use CommissionTask\Service\Math;
use DateTime;

class PrivateClient implements CalculateCommissionInterface
{
    use HttpAdapter;

    public const THRESHOLD = '1000.00';
    private static float $commission = 0.003; //TODO: must read from env


    /**
     * @var Math
     */
    private Math $math;

    /**
     * @var array
     */
    private array $userOperationHistory;

    /**
     * @var array
     */
    private array $lastWeekOperations;

    /**
     * @var Operation
     */
    private Operation $operation;

    /**
     * @param Math $math
     * @param Operation $operation
     * @param array $userOperationHistory
     */
    public function __construct(Math $math, Operation $operation, array $userOperationHistory)
    {
        $this->math = $math;
        $this->operation = $operation;
        $this->userOperationHistory = $userOperationHistory;
    }

    /**
     * @param string $amount
     * @return string
     */
    public function calculateCommissionFee(string $amount): string
    {
        $this->getLastWeeKOperations($this->operation->date);
        return $this->calcualateFinalCommission();
    }

    public function calcualateFinalCommission(): string
    {
        $remainedCoupon = self::THRESHOLD;
        $aggregateAmount = '0.00';
        $couponEndedBefore = false;
        $currentOperation = array_key_last($this->lastWeekOperations);

        foreach($this->lastWeekOperations as $operation) {
            $aggregateAmount = $this->math->add(
                $aggregateAmount, $this->convertToBaseRate($operation->amount, $operation->currency)
            );

            $remainedCoupon = $this->math->subtract(
                $remainedCoupon,
                $this->convertToBaseRate($operation->amount, $operation->currency)
            );

            if((float)$remainedCoupon < 0 ) {
                $operation->couponEnded = true;
            }
        }

        foreach($this->lastWeekOperations as $operation) {
            if($operation->id === $this->lastWeekOperations[$currentOperation]->id)
                continue;

            if(isset($operation->couponEnded) && $operation->couponEnded) {
                $couponEndedBefore = true;
            }
        }

        return $this->finalCommission($remainedCoupon, $couponEndedBefore, $aggregateAmount);
    }

    public function getLastWeeKOperations(string $date): array
    {
        $this->lastWeekOperations = [];
        foreach($this->userOperationHistory as $operation) {
            if (
                new DateTime($operation->date) >= (new DateTime($date))->modify('-7 day')
                &&
                new DateTime($operation->date) <= (new DateTime($date))
            ) {
                $this->lastWeekOperations[] = $operation;
            }
        }

        return $this->lastWeekOperations;
    }

    /**
     * @throws CurrencyIsNotValidException
     */
    public function convertToBaseRate(string $amount, string $currency): string
    {
        if ($currency === App::$exchangeRates->base) {
            return $amount;
        }
        
        if(!isset(App::$exchangeRates->rates->{$currency})) {
            throw new CurrencyIsNotValidException();
        }

        return $this->math->divide($amount, App::$exchangeRates->rates->{$currency});
    }

    /**
     * @throws CurrencyIsNotValidException
     */
    public function convertToUserRate(string $amount, string $currency): string
    {
        if ($currency === App::$exchangeRates->base) {
            return $amount;
        }

        if(!isset(App::$exchangeRates->rates->{$currency})) {
            throw new CurrencyIsNotValidException();
        }

        return $this->math->multiply($amount, App::$exchangeRates->rates->{$currency});
    }

    /**
     * @param string $remainedCoupon
     * @param bool $couponEndedBefore
     * @param string $aggregateAmount
     * @return string
     *
     * @throws CurrencyIsNotValidException
     */
    private function finalCommission(string $remainedCoupon, bool $couponEndedBefore, string $aggregateAmount): string
    {
        if (count($this->lastWeekOperations) <= 3 && (float)$remainedCoupon >= 0) {
            return '0.00';
        } elseif (count($this->lastWeekOperations) <= 3 && (float)$remainedCoupon < 0 && !$couponEndedBefore) {
            $value = $this->math->subtract($aggregateAmount, self::THRESHOLD);
            $commissionAmount =  $this->math->multiply($value, self::$commission);
        } elseif (count($this->lastWeekOperations) <= 3 && (float)$remainedCoupon < 0 && $couponEndedBefore) {
            $commissionAmount =  $this->math->multiply($this->operation->amount, self::$commission);
        } elseif (count($this->lastWeekOperations) > 3) {
            $commissionAmount =  $this->math->multiply($this->operation->amount, self::$commission);
        }

        return $this->convertToUserRate($commissionAmount, $this->operation->currency);
    }
}