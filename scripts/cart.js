document.addEventListener('DOMContentLoaded', function() {
// First, create the cart elements and append them to the body
function createCartElements() {
    // Create cart container
    const cartContainer = document.createElement('div');
    cartContainer.className = 'cart-container';
    
    // Create cart overlay
    const cartOverlay = document.createElement('div');
    cartOverlay.className = 'cart-overlay';
    
    // Create cart header
    const cartHeader = document.createElement('div');
    cartHeader.className = 'cart-header';
    
    const cartTitle = document.createElement('h2');
    cartTitle.textContent = 'Shopping Cart';
    
    const cartClose = document.createElement('button');
    cartClose.className = 'cart-close';
    cartClose.innerHTML = '<i class="ri-close-line"></i>';
    
    cartHeader.appendChild(cartTitle);
    cartHeader.appendChild(cartClose);
    
    // Create cart content
    const cartContent = document.createElement('div');
    cartContent.className = 'cart-content';
    
    const cartEmpty = document.createElement('div');
    cartEmpty.className = 'cart-empty';
    cartEmpty.innerHTML = '<p>Shopping cart is empty</p>';
    
    const cartItems = document.createElement('div');
    cartItems.className = 'cart-items';
    
    cartContent.appendChild(cartEmpty);
    cartContent.appendChild(cartItems);
    
    // Create cart footer
    const cartFooter = document.createElement('div');
    cartFooter.className = 'cart-footer';
    
    const cartTotal = document.createElement('div');
    cartTotal.className = 'cart-total';
    
    const totalLabel = document.createElement('span');
    totalLabel.textContent = 'Total:';
    
    const totalAmount = document.createElement('span');
    totalAmount.className = 'total-amount';
    totalAmount.textContent = '€0.00';
    
    cartTotal.appendChild(totalLabel);
    cartTotal.appendChild(totalAmount);
    
    const checkoutBtn = document.createElement('button');
    checkoutBtn.className = 'checkout-btn';
    checkoutBtn.textContent = 'Checkout';
    
    cartFooter.appendChild(cartTotal);
    cartFooter.appendChild(checkoutBtn);
    
    // Assemble cart container
    cartContainer.appendChild(cartHeader);
    cartContainer.appendChild(cartContent);
    cartContainer.appendChild(cartFooter);
    
    // Append to body
    document.body.appendChild(cartOverlay);
    document.body.appendChild(cartContainer);
    
    return {
    cartContainer,
    cartClose,
    cartOverlay,
    cartItems,
    cartEmpty,
    totalAmount
    };
}

// Create cart elements
const { cartContainer, cartClose, cartOverlay, cartItems, cartEmpty, totalAmount } = createCartElements();

// Find the cart toggle button
const cartToggle = document.querySelector('.cart-icon a') || document.querySelector('.ri-shopping-cart-line').parentElement;

// Sample cart data (empty initially)
let cart = [];

// Toggle cart when cart icon is clicked
cartToggle.addEventListener('click', function(e) {
    e.preventDefault();
    cartContainer.classList.add('active');
    cartOverlay.classList.add('active');
    updateCartDisplay();
});

// Close cart when close button is clicked
cartClose.addEventListener('click', function() {
    cartContainer.classList.remove('active');
    cartOverlay.classList.remove('active');
});

// Close cart when clicking on overlay
cartOverlay.addEventListener('click', function() {
    cartContainer.classList.remove('active');
    cartOverlay.classList.remove('active');
});

// Close cart when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && cartContainer.classList.contains('active')) {
    cartContainer.classList.remove('active');
    cartOverlay.classList.remove('active');
    }
});

// Function to update cart display
function updateCartDisplay() {
    if (cart.length === 0) {
    cartEmpty.style.display = 'flex';
    cartItems.style.display = 'none';
    totalAmount.textContent = '€0.00';
    } else {
    cartEmpty.style.display = 'none';
    cartItems.style.display = 'block';
    
    // Clear current items
    cartItems.innerHTML = '';
    
    // Calculate total
    let total = 0;
    
    // Add each item to the cart
    cart.forEach(item => {
        total += item.price * item.quantity;
        
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
        <div class="cart-item-details">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-price">€${item.price.toFixed(2)}</div>
            <div class="cart-item-quantity">
            <button class="quantity-btn minus" data-id="${item.id}">-</button>
            <span class="quantity-value">${item.quantity}</span>
            <button class="quantity-btn plus" data-id="${item.id}">+</button>
            </div>
        </div>
        <button class="cart-item-remove" data-id="${item.id}">
            <i class="ri-delete-bin-line"></i>
        </button>
        `;
        
        cartItems.appendChild(cartItem);
    });
    
    // Update total
    totalAmount.textContent = `$${total.toFixed(2)}`;
    
    // Add event listeners to quantity buttons and remove buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
        const id = this.dataset.id;
        decreaseQuantity(id);
        });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
        const id = this.dataset.id;
        increaseQuantity(id);
        });
    });
    
    document.querySelectorAll('.cart-item-remove').forEach(btn => {
        btn.addEventListener('click', function() {
        const id = this.dataset.id;
        removeFromCart(id);
        });
    });
    }
}

// Function to add item to cart
function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
    existingItem.quantity += 1;
    } else {
    cart.push({...product, quantity: 1});
    }
    
    updateCartDisplay();
}

// Function to remove item from cart
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartDisplay();
}

// Function to increase item quantity
function increaseQuantity(id) {
    const item = cart.find(item => item.id === id);
    if (item) {
    item.quantity += 1;
    updateCartDisplay();
    }
}

// Function to decrease item quantity
function decreaseQuantity(id) {
    const item = cart.find(item => item.id === id);
    if (item) {
    item.quantity -= 1;
    if (item.quantity <= 0) {
        removeFromCart(id);
    } else {
        updateCartDisplay();
    }
    }
}

// Example function to add a product (for testing)
window.addProductToCart = function(id, name, price, image) {
    addToCart({id, name, price, image});
    cartContainer.classList.add('active');
    cartOverlay.classList.add('active');
};
});
