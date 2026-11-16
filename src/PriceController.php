<?php

declare(strict_types=1);

namespace App\Controllers;

use InvalidArgumentException;

final class PriceController
{
    public function convertEurosToDollars(float $euros, float $exchangeRate): float
    {
        $this->validateAmount($euros, 'Los euros');
        $this->validateExchangeRate($exchangeRate);

        return round($euros * $exchangeRate, 2, PHP_ROUND_HALF_UP);
    }

    public function convertDollarsToEuros(float $dollars, float $exchangeRate): float
    {
        $this->validateAmount($dollars, 'Los dólares');
        $this->validateExchangeRate($exchangeRate);

        return round($dollars / $exchangeRate, 2, PHP_ROUND_HALF_UP);
    }

    public function calculateDiscount(float $price, float $discountPercentage): float
    {
        $this->validateAmount($price, 'El precio');

        if (!is_finite($discountPercentage) || $discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('El descuento debe estar entre 0 y 100.');
        }

        return round($price * (1 - $discountPercentage / 100), 2, PHP_ROUND_HALF_UP);
    }

    private function validateAmount(float $amount, string $name): void
    {
        if (!is_finite($amount) || $amount < 0) {
            throw new InvalidArgumentException("$name debe ser finito y no negativo.");
        }
    }

    private function validateExchangeRate(float $exchangeRate): void
    {
        if (!is_finite($exchangeRate) || $exchangeRate <= 0) {
            throw new InvalidArgumentException('El tipo de cambio debe ser finito y positivo.');
        }
    }
}
