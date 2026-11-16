# Unitario Prices

`PriceController` ofrece conversiones entre euros y dólares usando el tipo de
cambio recibido en cada llamada, y calcula el precio tras aplicar un descuento.
Los importes resultantes se redondean a dos decimales. Los importes deben ser
finitos y no negativos; el tipo de cambio debe ser positivo y el descuento debe
estar entre 0 % y 100 %.

## Ejecutar los tests

```sh
composer install
composer test
```

## Abrir la página

Abre `index.html` directamente en el navegador; no necesita servidor ni
instalaciones para hacer los cálculos.

La aplicación de PHP también se puede iniciar desde la carpeta del proyecto:

```sh
php -S localhost:8000
```

Después abre <http://localhost:8000>. Introduce el tipo de cambio que quieras
usar para la conversión; el valor inicial de 1,10 es solo un ejemplo.
