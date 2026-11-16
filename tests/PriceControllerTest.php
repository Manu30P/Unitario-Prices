<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controllers\PriceController;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PriceControllerTest extends TestCase
{
    private PriceController $controller;

    protected function setUp(): void
    {
        $this->controller = new PriceController();
    }

    public function testConvertsEurosToDollarsUsingTheProvidedExchangeRate(): void
    {
        self::assertSame(11.0, $this->controller->convertEurosToDollars(10.0, 1.1));
    }

    public function testRoundsEuroToDollarConversionToCents(): void
    {
        self::assertSame(0.06, $this->controller->convertEurosToDollars(0.05, 1.1));
    }

    public function testConvertsDollarsToEurosUsingTheProvidedExchangeRate(): void
    {
        self::assertSame(10.0, $this->controller->convertDollarsToEuros(11.0, 1.1));
    }

    public function testCalculatesDiscountedPrice(): void
    {
        self::assertSame(80.0, $this->controller->calculateDiscount(100.0, 20.0));
    }

    public function testRoundsDiscountedPriceToCents(): void
    {
        self::assertSame(16.99, $this->controller->calculateDiscount(19.99, 15.0));
    }

    public function testZeroAndFullDiscounts(): void
    {
        self::assertSame(25.0, $this->controller->calculateDiscount(25.0, 0.0));
        self::assertSame(0.0, $this->controller->calculateDiscount(25.0, 100.0));
    }

    #[DataProvider('invalidAmounts')]
    public function testRejectsInvalidAmounts(float $amount): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->controller->convertEurosToDollars($amount, 1.1);
    }

    public static function invalidAmounts(): array
    {
        return [
            'negative amount' => [-1.0],
            'infinite amount' => [INF],
            'not a number' => [NAN],
        ];
    }

    #[DataProvider('invalidDiscounts')]
    public function testRejectsDiscountsOutsideTheValidRange(float $discount): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->controller->calculateDiscount(100.0, $discount);
    }

    public static function invalidDiscounts(): array
    {
        return [
            'negative discount' => [-1.0],
            'discount over 100 percent' => [100.01],
            'infinite discount' => [INF],
            'not a number' => [NAN],
        ];
    }

    public function testRejectsNonPositiveExchangeRate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->controller->convertDollarsToEuros(10.0, 0.0);
    }
}
