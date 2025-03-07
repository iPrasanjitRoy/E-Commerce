<!--start quick view-->

<!-- Modal -->
<div class="modal fade" id="QuickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-0">

            <div class="modal-body">
                <div class="row g-3">

                    <!-- Product Images -->
                    <div class="col-12 col-xl-6">
                        <div class="wrap-modal-slider">

                            <div class="slider-for" id="quick-view-slider">
                                <!-- Images will be inserted here dynamically -->




                            </div>

                            <div class="slider-nav mt-3" id="quick-view-thumbnails">
                                <!-- Thumbnails will be inserted here dynamically -->
                            </div>

                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="col-12 col-xl-6">
                        <div class="product-info">

                            <h4 class="product-title fw-bold mb-1" id="quick-view-title"></h4>
                            <p class="mb-0" id="quick-view-description"></p>

                            <hr>

                            <!-- Product Price -->
                            <div class="product-price d-flex align-items-center gap-3" id="quick-view-price">
                                <!-- Prices will be inserted dynamically -->
                            </div>

                            <p class="fw-bold mb-0 mt-1 text-success">inclusive of all taxes</p>

                            <!-- More Colors -->
                            <div class="more-colors mt-3">
                                <h6 class="fw-bold mb-3">More Colors</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap" id="quick-view-colors">
                                    <!-- Colors will be inserted dynamically -->
                                </div>
                            </div>

                            <!-- Size Chart -->
                            <div class="size-chart mt-3">
                                <h6 class="fw-bold mb-3">Select Size</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap" id="quick-view-sizes">
                                    <!-- Sizes will be inserted dynamically -->
                                </div>
                            </div>

                            <!-- Add to Cart & Wishlist Buttons -->
                            <div class="cart-buttons mt-3">
                                <div class="buttons d-flex flex-column gap-3 mt-4">
                                    <a href="javascript:;" id="quick-view-cart"
                                        class="btn btn-lg btn-dark btn-ecomm px-5 py-3 flex-grow-1 quick-view-cart"><i
                                            class="bi bi-basket2 me-2"></i>Add to Cart</a>

                                    <a href="javascript:;" id="quick-view-wishlist"
                                        class="btn btn-lg btn-outline-dark btn-ecomm px-5 py-3"><i
                                            class="bi bi-suit-heart me-2"></i>Wishlist</a>
                                </div>
                            </div>


                            <hr class="my-3">

                            <div class="product-share">
                                <h6 class="fw-bold mb-3">Share This Product</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="">
                                        <button type="button" class="btn-social bg-twitter"><i
                                                class="bi bi-twitter"></i></button>
                                    </div>
                                    <div class="">
                                        <button type="button" class="btn-social bg-facebook"><i
                                                class="bi bi-facebook"></i></button>
                                    </div>
                                    <div class="">
                                        <button type="button" class="btn-social bg-linkden"><i
                                                class="bi bi-linkedin"></i></button>
                                    </div>
                                    <div class="">
                                        <button type="button" class="btn-social bg-youtube"><i
                                                class="bi bi-youtube"></i></button>
                                    </div>
                                    <div class="">
                                        <button type="button" class="btn-social bg-pinterest"><i
                                                class="bi bi-pinterest"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>

        </div>
    </div>
</div>
<!--end quick view-->


<script>
    $(document).ready(function () {
        $(document).on('click', '.quick-view-btn', function () {
            let productId = $(this).data('id');

            $.ajax({
                url: 'fetch-product.php',
                type: 'POST',
                data: { action: 'quickview', product_id: productId },
                dataType: 'json',
                success: function (data) {
                    if (!data.error) {

                        $('#quick-view-title').text(data.product_name);
                        $('#quick-view-description').text(data.description);

                        let sliderHtml = '';
                        let thumbnailHtml = '';
                        let images = [data.image_one, data.image_two, data.image_three].filter(img => img);

                        images.forEach(img => {
                            sliderHtml += `<div><img src="backend/${img}" alt="" class="img-fluid"></div>`;
                            thumbnailHtml += `<div><img src="backend/${img}" alt="" class="img-fluid"></div>`;
                        });

                        /* THE ISSUE HAPPENS BECAUSE SLICK.JS DOES NOT AUTOMATICALLY DETECT DYNAMICALLY INSERTED CONTENT.  */
                        /*  WHEN YOU MANUALLY SET #quick-view-slider CONTENT WITH  .html(sliderHtml), SLICK IS UNAWARE OF THE NEW IMAGES AND DOES NOT INITIALIZE PROPERLY */
                        /*  DESTROY EXISTING SLIDER TO PREVENT ISSUES */
                        if ($('.slider-for').hasClass('slick-initialized')) {
                            $('.slider-for').slick('unslick');
                        }
                        if ($('.slider-nav').hasClass('slick-initialized')) {
                            $('.slider-nav').slick('unslick');
                        }

                        $('#quick-view-slider').html(sliderHtml);
                        $('#quick-view-thumbnails').html(thumbnailHtml);


                        /*  REINITIALIZE SLICK */
                        $('.slider-for').slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            fade: true,
                            asNavFor: '.slider-nav'
                        });

                        $('.slider-nav').slick({
                            slidesToShow: 3,
                            slidesToScroll: 1,
                            asNavFor: '.slider-for',
                            focusOnSelect: true
                        });


                        let priceHtml = '';
                        let sizeHtml = '';
                        let colorHtml = '';
                        let colors = new Map();
                        let sizes = new Map();
                        let priceCombinations = data.price_combinations;


                        if (priceCombinations.length > 0) {
                            priceHtml = `<div class="h5 fw-bold">₹${parseFloat(priceCombinations[0].price).toFixed(2)}</div>`;
                        } else {
                            priceHtml = '<div class="h5 text-muted">Price Not Available</div>';
                        }


                        priceCombinations.forEach(combination => {
                            sizes.set(combination.size_id, combination.size_name);
                            colors.set(combination.colour_id, combination.colour_name);
                        });


                        sizes.forEach((name, id) => {
                            sizeHtml += `<button type="button" class="btn btn-outline-dark rounded-0 size-btn" data-size-id="${id}">${name}</button>`;
                        });



                        colors.forEach((name, id) => {
                            colorHtml += `<div class="color-box" style="background-color: ${name.toLowerCase()};"  data-color-id="${id}" title="${name}"></div>`;
                        });

                        $('#quick-view-price').html(priceHtml);
                        $('#quick-view-sizes').html(sizeHtml);
                        $('#quick-view-colors').html(colorHtml);

                        let selectedSize = null;
                        let selectedColor = null;
                        let selectedCombinationId = null;

                        $('.size-btn').click(function () {
                            $('.size-btn').removeClass('active');
                            $(this).addClass('active');

                            selectedSize = $(this).data('size-id');
                            updatePrice();
                        });


                        $('.color-box').click(function () {
                            $('.color-box').removeClass('active');
                            $(this).addClass('active');

                            selectedColor = $(this).data('color-id');
                            updatePrice();
                        });



                        function updatePrice() {
                            if (selectedSize && selectedColor) {
                                let selectedCombination = priceCombinations.find(combination =>
                                    combination.size_id == selectedSize && combination.colour_id == selectedColor
                                );

                                if (selectedCombination) {
                                    $('#quick-view-price').html(`<div class="h5 fw-bold">₹${parseFloat(selectedCombination.price).toFixed(2)}</div>`);

                                    selectedCombinationId = selectedCombination.combination_id;
                                    $('#quick-view-cart').attr('data-combination-id', selectedCombinationId);
                                    $('#quick-view-wishlist').attr('data-combination-id', selectedCombinationId);



                                    /* 🛒 CHECK IF ALREADY IN CART */
                                    if (selectedCombination.in_cart) {
                                        $('#quick-view-cart')
                                            .text('Remove from Cart')
                                            .addClass('remove-cart')
                                            .removeClass('quick-view-cart');
                                    } else {
                                        $('#quick-view-cart')
                                            .text('Add to Cart')
                                            .addClass('quick-view-cart')
                                            .removeClass('remove-cart');
                                    }


                                } else {
                                    $('#quick-view-price').html('<div class="h5 text-danger">Price Not Available</div>');

                                    selectedCombinationId = null;
                                    $('#quick-view-cart').attr('data-combination-id', '');
                                    $('#quick-view-wishlist').attr('data-combination-id', '');


                                }
                            }
                        }

                        // $('#quick-view-cart').attr('href', 'cart.php?add=' + data.product_id);
                        // $('#quick-view-wishlist').attr('href', 'wishlist.php?add=' + data.product_id);
                        $('#QuickViewModal').modal('show');
                    }
                }
            });
        });
    });



    $(document).on('click', '.quick-view-cart', function () {
        let $btn = $(this);
        let combinationId = $(this).attr('data-combination-id');

        if (!combinationId) {
            alert('Please select a size and color before adding to cart.');
            return;
        }
        console.log(combinationId);

        /*  DISABLE THE BUTTON AND SHOW LOADING STATE */
        $btn.prop('disabled', true).text('Adding...');

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { add_to_cart_id: combinationId },
            dataType: 'json',
            success: function (response) {

                if (response.status === 'error') {
                    alert(response.message);
                    $btn.prop('disabled', false).text('Add to Cart'); /* RESET TO NORMAL */


                } else if (response.status === 'success') {
                    alert(response.message);

                    window.loadCart();

                    $btn.text('Remove from Cart').addClass('remove-cart').removeClass('quick-view-cart').prop('disabled', false);

                }

            },
            error: function () {
                alert('Failed to add product to cart.');
                $btn.prop('disabled', false).text('Add to Cart');
            },

        });
    });


    $(document).on('click', '.remove-cart', function () {
        let $btn = $(this);
        let combinationId = $btn.attr('data-combination-id');

        console.log(combinationId);

        $btn.prop('disabled', true).text('Removing...');

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { remove_from_cart_id: combinationId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);
                    $btn.text('Add to Cart').removeClass('remove-cart').addClass('quick-view-cart').prop('disabled', false);

                } else {
                    alert(response.message);
                    $btn.prop('disabled', false).text('Remove from Cart');
                }
            },
            error: function () {
                alert('Failed to remove product.');
                $btn.prop('disabled', false).text('Remove from Cart');
            }
        });
    });




</script>