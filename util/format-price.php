<?php
function formatPrice($price, $selectedCurrency) {
    if ($selectedCurrency === 'eur') {
        // Convert price to EUR and format with two decimal places
        $price = number_format((float)$price * 0.85, 2, '.', ','); // Example conversion rate
        return $price . ' €';
    } else {
        // Format price with two decimal places
        return '$ ' . number_format((float)$price, 2, '.', ',');
    }
}