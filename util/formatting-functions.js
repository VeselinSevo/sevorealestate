export function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

export function formatPrice(price, selectedCurrency) {
    // Ensure price is a valid number
    price = parseFloat(price);

    console.log(numeral(price).format('0,0.00') + ' €')
    // Check if selectedCurrency is EUR
    if (selectedCurrency === 'eur') {
        // Convert price to EUR and format with two decimal places
        price = (price * 0.85).toFixed(2); // Example conversion rate
        // Format price with numeral.js
        return numeral(price).format('0,0.00') + ' €';
    } else {
        // Format price with numeral.js
        return numeral(price).format('$0,0.00');
    }
}
