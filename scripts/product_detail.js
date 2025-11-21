document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.thumbnail img');
    const productName = document.querySelector('.product-name');
    const productSku = document.querySelector('.product-sku');
    const productPrice = document.querySelector('.price');
    const productDescription = document.querySelector('.product-description p');
    const sizeGrid = document.querySelector('.size-grid');
    const addToCartBtn = document.querySelector('.add-to-cart');
    let selectedSize = null;

    // Mock data for out-of-stock sizes
    const outOfStockSizes = ['43', '44.5'];

    // Handle thumbnail clicks
    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const productId = this.dataset.productId;
            mainImage.src = this.src;
            mainImage.alt = this.alt;

            // Fetch and update product details
            fetch(`get_product_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    productName.textContent = data.name;
                    productSku.textContent = `SKU: ${data.sku}`;
                    productPrice.textContent = `€${parseFloat(data.price).toFixed(2)}`;
                    productDescription.innerHTML = data.description;

                    // Update size buttons
                    sizeGrid.innerHTML = '';
                    data.sizes.split(',').forEach(size => {
                        const button = document.createElement('button');
                        button.className = 'size-btn';
                        button.dataset.size = size;
                        button.textContent = size;
                        
                        if (outOfStockSizes.includes(size)) {
                            button.classList.add('out-of-stock');
                        } else if (selectedSize === size) {
                            button.classList.add('selected');
                        }
                        
                        sizeGrid.appendChild(button);
                    });

                    // Update URL without page reload
                    const newUrl = `product_detail.php?id=${productId}`;
                    window.history.pushState({ path: newUrl }, '', newUrl);
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Handle size selection
    sizeGrid.addEventListener('click', function(e) {
        if (e.target.classList.contains('size-btn')) {
            if (e.target.classList.contains('out-of-stock')) {
                return; // Prevent selecting out-of-stock sizes
            }

            const buttons = sizeGrid.querySelectorAll('.size-btn');
            
            if (selectedSize === e.target.dataset.size) {
                // Deselect if clicking the same size
                e.target.classList.remove('selected');
                selectedSize = null;
                addToCartBtn.classList.remove('ready');
            } else {
                // Select new size
                buttons.forEach(btn => btn.classList.remove('selected'));
                e.target.classList.add('selected');
                selectedSize = e.target.dataset.size;
                addToCartBtn.classList.add('ready');
            }
        }
    });

    // Handle add to cart
    addToCartBtn.addEventListener('click', function() {
        if (!selectedSize) {
            const sizeSelection = document.querySelector('.size-selection');
            sizeSelection.scrollIntoView({ behavior: 'smooth' });
            
            // Add visual feedback
            sizeSelection.classList.add('highlight');
            setTimeout(() => sizeSelection.classList.remove('highlight'), 2000);
            
            // Show tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'size-tooltip';
            tooltip.textContent = 'Please select a size';
            sizeSelection.appendChild(tooltip);
            
            setTimeout(() => tooltip.remove(), 3000);
            return;
        }
        // Add to cart logic here
        console.log(`Adding size ${selectedSize} to cart`);
    });

    // Handle wishlist
    const wishlistBtn = document.querySelector('.add-to-wishlist');
    wishlistBtn.addEventListener('click', function() {
        this.classList.toggle('active');
        const icon = this.querySelector('i');
        icon.classList.toggle('ri-heart-line');
        icon.classList.toggle('ri-heart-fill');
    });
});