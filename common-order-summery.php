<h5 class="fw-bold mb-4">Order Summary</h5>

<div class="hstack align-items-center justify-content-between">
  <p class="mb-0">Bag Total</p>
  <p class="mb-0">₹<?php echo number_format($totalAmount, 2); ?> </p>
</div>

<hr>

<div class="hstack align-items-center justify-content-between">
  <p class="mb-0">Bag Discount</p>
  <p class="mb-0 text-success"> - ₹<?php echo number_format($totalDiscount, 2); ?> </p>
</div>
<hr>

<div class="hstack align-items-center justify-content-between">
  <p class="mb-0">GST (<?php echo $gstRate * 100; ?>%)</p>
  <p class="mb-0">₹<?php echo number_format($gstAmount, 2); ?></p>
</div>
<hr>



<div class="hstack align-items-center justify-content-between">
  <p class="mb-0">Delivery</p>
  <p class="mb-0">₹<?php echo number_format($deliveryCharge, 2); ?></p>
</div>
<hr>



<div class="hstack align-items-center justify-content-between fw-bold text-content">
  <p class="mb-0">Total Amount</p>
  <p class="mb-0">₹<?php echo number_format($netTotalAmount, 2); ?></p>
</div>