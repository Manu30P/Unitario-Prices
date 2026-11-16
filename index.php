<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\PriceController;

$controller = new PriceController();
$form = [
    'operation' => is_string($_POST['operation'] ?? null) ? $_POST['operation'] : 'euros-to-dollars',
    'amount' => is_string($_POST['amount'] ?? null) ? $_POST['amount'] : '',
    'exchange_rate' => is_string($_POST['exchange_rate'] ?? null) ? $_POST['exchange_rate'] : '1.10',
    'discount' => is_string($_POST['discount'] ?? null) ? $_POST['discount'] : '',
];
$result = null;
$error = null;

$readNumber = static function (string $field, string $label) use ($form): float {
    $value = trim($form[$field]);
    if ($value === '' || !is_numeric($value) || !is_finite((float) $value)) {
        throw new InvalidArgumentException("Introduce un valor numérico válido para $label.");
    }

    return (float) $value;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $amount = $readNumber('amount', 'el importe');

        switch ($form['operation']) {
            case 'euros-to-dollars':
                $converted = $controller->convertEurosToDollars(
                    $amount,
                    $readNumber('exchange_rate', 'el tipo de cambio')
                );
                $result = number_format($converted, 2, ',', '.') . ' $';
                break;

            case 'dollars-to-euros':
                $converted = $controller->convertDollarsToEuros(
                    $amount,
                    $readNumber('exchange_rate', 'el tipo de cambio')
                );
                $result = number_format($converted, 2, ',', '.') . ' €';
                break;

            case 'discount':
                $discountedPrice = $controller->calculateDiscount(
                    $amount,
                    $readNumber('discount', 'el descuento')
                );
                $result = number_format($discountedPrice, 2, ',', '.') . ' €';
                break;

            default:
                throw new InvalidArgumentException('Selecciona una operación válida.');
        }
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calculadora de precios</title>
    <style>
        :root { color-scheme: light; font-family: system-ui, sans-serif; color: #172033; background: #f3f6fb; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        main { width: min(100%, 520px); box-sizing: border-box; padding: 32px; border-radius: 16px; background: #fff; box-shadow: 0 12px 36px #17203318; }
        h1 { margin: 0 0 8px; font-size: 1.7rem; }
        p { margin: 0 0 24px; color: #5b6578; }
        label { display: block; margin: 16px 0 6px; font-weight: 600; }
        input, select, button { box-sizing: border-box; width: 100%; padding: 11px 12px; border: 1px solid #cbd3df; border-radius: 8px; font: inherit; }
        button { margin-top: 22px; border: 0; color: white; background: #2459d3; font-weight: 700; cursor: pointer; }
        button:hover { background: #1948b4; }
        .result, .error { margin-top: 20px; padding: 14px; border-radius: 8px; }
        .result { background: #eaf7ef; color: #176438; font-size: 1.15rem; }
        .error { background: #fff0ef; color: #a52b23; }
        @media (max-width: 480px) { main { padding: 24px; } }
    </style>
</head>
<body>
<main>
    <h1>Calculadora de precios</h1>
    <p>Convierte euros y dólares o calcula un precio con descuento.</p>

    <form method="post">
        <label for="operation">Operación</label>
        <select id="operation" name="operation">
            <option value="euros-to-dollars" <?= $form['operation'] === 'euros-to-dollars' ? 'selected' : '' ?>>Euros a dólares</option>
            <option value="dollars-to-euros" <?= $form['operation'] === 'dollars-to-euros' ? 'selected' : '' ?>>Dólares a euros</option>
            <option value="discount" <?= $form['operation'] === 'discount' ? 'selected' : '' ?>>Aplicar descuento (precio en euros)</option>
        </select>

        <label for="amount">Importe</label>
        <input id="amount" name="amount" type="number" min="0" step="any" value="<?= $escape($form['amount']) ?>" required>

        <label for="exchange_rate">Tipo de cambio (dólares por euro)</label>
        <input id="exchange_rate" name="exchange_rate" type="number" min="0" step="any" value="<?= $escape($form['exchange_rate']) ?>">

        <label for="discount">Descuento (%)</label>
        <input id="discount" name="discount" type="number" min="0" max="100" step="any" value="<?= $escape($form['discount']) ?>">

        <button type="submit">Calcular</button>
    </form>

    <?php if ($result !== null): ?>
        <div class="result" role="status">Resultado: <strong><?= $escape($result) ?></strong></div>
    <?php endif; ?>
    <?php if ($error !== null): ?>
        <div class="error" role="alert"><?= $escape($error) ?></div>
    <?php endif; ?>
</main>
</body>
</html>
