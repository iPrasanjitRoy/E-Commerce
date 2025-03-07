< !--Cart Sidebar-->
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasRight"
        aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-header bg-section-2">

            <h5 class="mb-0 fw-bold" id="cartItemCount">0 items in the cart</h5>

            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

        </div>

        <div class="offcanvas-body">

            <div class="cart-list" id="cartItems">
                <!-- Cart items will be inserted dynamically here -->
            </div>

        </div>

        <div class="offcanvas-footer p-3 border-top">

            <div class="d-grid">
                <a href="shop-cart.php" class="btn btn-lg btn-dark btn-ecomm px-5 py-3">Checkout</a>
            </div>

        </div>
    </div>

    <script>


        $(document).ready(function () {
            loadCart();

            function loadCart() {
                $.ajax({
                    url: 'cart.php',
                    type: 'GET',
                    data: { fetchCartItems: true },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            renderCart(response.cartItems);
                        }
                    },
                    error: function () {
                        console.log('Failed to load cart.');
                    }
                });
            }
            window.loadCart = loadCart;




            function renderCart(cartItems) {
                let cartList = $('#cartItems');
                cartList.empty();

                let totalItems = 0;

                if (cartItems.length === 0) {
                    cartList.append('<p class="text-center">Your cart is empty.</p>');

                } else {
                    cartItems.forEach(item => {

                        totalItems += parseInt(item.quantity, 10);


                        let cartItemHTML = `
                                            <div class="d-flex align-items-center gap-3 cart-item" data-id="${item.combination_id}">

                                                <div class="bottom-product-img">
                                                    <a href="product-details.html">
                                                        <img src="backend/${item.image}" width="60" alt="">
                                                    </a>
                                                </div>


                                                <div>
                                                    <h6 class="mb-0 fw-light mb-1">${item.name}</h6>
                                                    <p class="mb-0"><strong>${item.quantity} X ₹${item.price}</strong></p>
                                                </div>



                                                <div class="quantity-controls d-flex align-items-center">
                                                    <button class="btn btn-sm btn-outline-dark decrement" data-id="${item.combination_id}">-</button>

                                                    <span class="mx-2">${item.quantity}</span>
                                                    
                                                    <button class="btn btn-sm btn-outline-dark increment" data-id="${item.combination_id}">+</button>
                                                </div>


                                                <div class="ms-auto fs-5">
                                                    <a href="javascript:" class="link-dark remove-item" data-id="${item.combination_id}">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>

                                            </div>
                                            <hr>`;


                        cartList.append(cartItemHTML);
                    });
                }

                $('#cartItemCount').text(`${totalItems} items in the cart`);
            }



            /* Increment Quantity */
            $(document).on('click', '.increment', function () {
                let combinationId = $(this).data('id');
                updateCartQuantity(combinationId, 1);
            });

            /* Decrement Quantity */
            $(document).on('click', '.decrement', function () {
                let combinationId = $(this).data('id');
                updateCartQuantity(combinationId, -1);
            });



            function updateCartQuantity(combinationId, change) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { update_quantity_id: combinationId, change: change },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            loadCart();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Failed to update quantity.');
                    }
                });
            }





            /* Remove Item */
            $(document).on('click', '.remove-item', function () {
                let combinationId = $(this).data('id');
                removeFromCart(combinationId);
            });

            function removeFromCart(combinationId) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: { remove_from_cart_id: combinationId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            loadCart();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Failed to remove product.');
                    }
                });
            }



        });



    </script>