<?php

require_once 'Products/Product.php';
require_once 'Products/DigitalProduct.php';
require_once 'Products/PieceProduct.php';
require_once 'Products/WeightProduct.php';

$digitalProduct = new DigitalProduct("Электронная книга", 1000);
$pieceProduct = new PieceProduct("Смартфон", 50000);
$weightProduct = new WeightProduct("Яблоки", 150);

echo "=== ДЕМОНСТРАЦИЯ ТОВАРНОЙ НОМЕНКЛАТУРЫ ===\n\n";

// Цифровой товар
$digitalQuantity = 3;
$digitalCost = $digitalProduct->calculateCost($digitalQuantity);
echo "Цифровой товар: $digitalProduct->name\n";
echo "Базовая цена физического аналога: $digitalProduct->basePrice руб.\n";
echo "Количество: $digitalQuantity\n";
echo "Финальная стоимость: $digitalCost руб.\n\n";

// Штучный товар
$pieceQuantity = 2;
$pieceCost = $pieceProduct->calculateCost($pieceQuantity);
echo "Штучный товар: $pieceProduct->name\n";
echo "Цена за штуку: $pieceProduct->basePrice руб.\n";
echo "Количество: $pieceQuantity шт.\n";
echo "Финальная стоимость: $pieceCost руб.\n\n";

// Весовой товар
$weightQuantity = 2.5;
$weightCost = $weightProduct->calculateCost($weightQuantity);
echo "Весовой товар: $weightProduct->name\n";
echo "Цена за кг: $weightProduct->basePrice руб.\n";
echo "Количество: $weightQuantity кг\n";
echo "Финальная стоимость: $weightCost руб.\n\n";

$totalRevenue = $digitalCost + $pieceCost + $weightCost;
echo "=== ОБЩАЯ СТАТИСТИКА ===\n";
echo "Общий доход с продаж: $totalRevenue руб.\n";