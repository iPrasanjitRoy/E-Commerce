<div class="card rounded-0">

    <div class="card-header bg-light">
        <div class="d-flex align-items-center">

            <div class="flex-grow-1">
                <h5 class="fw-bold mb-0">Saved Address</h5>
            </div>

            <div class="">
                <button type="button" class="btn btn-ecomm" data-bs-toggle="modal" data-bs-target="#NewAddress"><i
                        class="bi bi-plus-lg me-2"></i>Add New Address</button>
            </div>

        </div>
    </div>

    <?php

    if (!isset($_SESSION['user_id'])) {
        echo "<p class='text-danger'>Please log in to view saved addresses.</p>";
        exit;
    }

    $customer_id = $_SESSION['user_id'];


    $query = "SELECT * FROM `address` WHERE customer_id = '$customer_id' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    $defaultAddress = null;
    $otherAddresses = [];

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['is_default'] == 1) {
            $defaultAddress = $row;
        } else {
            $otherAddresses[] = $row;
        }
    }

    ?>


    <div class="card-body">
        <?php if ($defaultAddress): ?>

            <h6 class="fw-bold mb-3 py-2 px-3 bg-light">Default Address</h6>
            <div class="card rounded-0 mb-3">

                <div class="card-body">
                    <div class="d-flex flex-column flex-xl-row gap-3">
                        <div class="address-info form-check flex-grow-1">

                            <input class="form-check-input address-radio" type="radio" name="flexradioaddress"
                                id="flexRadioDefaultAddress" data-id="<?= $defaultAddress['id']; ?>"
                                value="<?= $defaultAddress['id']; ?>" checked>

                            <label class="form-check-label" for="flexRadioDefaultAddress">

                                <span class="fw-bold mb-0 h5">
                                    <?php echo htmlspecialchars($defaultAddress['name']); ?></span><br>

                                <?php echo htmlspecialchars($defaultAddress['address']); ?>,
                                <?php echo htmlspecialchars($defaultAddress['city_village']); ?><br>

                                <?php echo htmlspecialchars($defaultAddress['district']); ?>,
                                <?php echo htmlspecialchars($defaultAddress['state']); ?> -
                                <?php echo htmlspecialchars($defaultAddress['pin_code']); ?><br>

                                Mobile: <span class="text-dark fw-bold">
                                    <?= htmlspecialchars($defaultAddress['mobile_no']); ?>
                                </span>
                            </label>

                        </div>

                        <div class="d-none d-xl-block vr"></div>

                        <div class="d-grid gap-2 align-self-start align-self-xl-center">

                            <button type="button" class="btn btn-outline-dark px-5 btn-ecomm removeaddress"
                                data-id="<?= $defaultAddress['id']; ?>">Remove</button>

                            <button type="button" class="btn btn-outline-dark px-5 btn-ecomm editaddress"
                                data-bs-toggle="modal" data-bs-target="#EditAddress"
                                data-id="<?= $defaultAddress['id']; ?>">Edit</button>
                        </div>

                    </div>
                </div>
            </div>

        <?php endif; ?>





        <h6 class="fw-bold mb-3 py-2 px-3 bg-light">Other Address</h6>

        <?php if (!empty($otherAddresses)): ?>
            <?php foreach ($otherAddresses as $address): ?>

                <div class="card rounded-0 mb-3">

                    <div class="card-body">
                        <div class="d-flex flex-column flex-xl-row gap-3">
                            <div class="address-info form-check flex-grow-1">

                                <input class="form-check-input address-radio" type="radio" name="flexradioaddress"
                                    id="flexRadioOtherAddress<?php echo $address['id']; ?>"
                                    data-id="<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>">

                                <label class="form-check-label" for="flexRadioOtherAddress<?php echo $address['id']; ?>">

                                    <span class="fw-bold mb-0 h5"><?php echo htmlspecialchars($address['name']); ?></span><br>

                                    <?php echo htmlspecialchars($address['address']); ?>,
                                    <?php echo htmlspecialchars($address['city_village']); ?><br>

                                    <?php echo htmlspecialchars($address['district']); ?>,
                                    <?php echo htmlspecialchars($address['state']); ?>
                                    -
                                    <?php echo htmlspecialchars($address['pin_code']); ?><br>

                                    Mobile: <span
                                        class="text-dark fw-bold"><?= htmlspecialchars($address['mobile_no']); ?></span>
                                </label>

                            </div>

                            <div class="d-grid gap-2 align-self-start align-self-xl-center">

                                <button type="button" class="btn btn-outline-dark px-5 btn-ecomm removeaddress"
                                    data-id="<?php echo $address['id']; ?>">Remove</button>

                                <button type="button" class="btn btn-outline-dark px-5 btn-ecomm editaddress"
                                    data-bs-toggle="modal" data-bs-target="#EditAddress"
                                    data-id="<?php echo $address['id']; ?>">Edit
                                </button>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>


            </div>

        <?php endif; ?>


        <div class="card rounded-0">
            <div class="card-body">
                <button type="button" class="btn btn-outline-dark btn-ecomm" data-bs-toggle="modal"
                    data-bs-target="#NewAddress">Add New Address</button>
            </div>
        </div>



    </div>

</div>