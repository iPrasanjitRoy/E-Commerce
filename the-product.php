<div class="card">
    <div class="position-relative overflow-hidden">

        <div
            class="product-options d-flex align-items-center justify-content-center gap-2 mx-auto position-absolute bottom-0 start-0 end-0">

            <a href="javascript:;" class="wishlistbutton" data-product-id="<?= $product['product_id']; ?>">
                <i class="bi bi-heart"></i></a>

            <a href="javascript:;" class="cartbutton" data-combination-id="<?= $product['combination_id']; ?>">
                <i class="bi bi-cart-check-fill"></i></a>

            <a href="javascript:;" class="quick-view-btn" data-id="<?php echo $product['product_id']; ?>"
                data-bs-toggle="modal" data-bs-target="#QuickViewModal"><i class="bi bi-zoom-in"></i></a>
        </div>



        <a href="javascript:;">
            <img src="backend/<?php echo htmlspecialchars($product['image_one']); ?>" class="card-img-top"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>">
        </a>

    </div>

    <div class="card-body">
        <div class="product-info text-center">
            <h6 class="mb-1 fw-bold product-name"><?php echo htmlspecialchars($product['product_name']); ?></h6>



            <p class="mb-0 h6 fw-bold product-price">
                <?php echo ($product['price'] !== 'N/A') ? "₹" . number_format($product['price'], 2) : "Price Not Available"; ?>
            </p>

        </div>
    </div>
</div>