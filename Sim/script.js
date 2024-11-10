// script.js
let cart = [];

function addToCart(productId, productName, productPrice) {
    const product = { id: productId, name: productName, price: productPrice };
    cart.push(product);
    alert(productName + " has been added to your cart!");
}

// Example usage:
addToCart(1, "Round Brilliant Cut", 5000);
