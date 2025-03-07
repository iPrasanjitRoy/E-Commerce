<?php
$current_page = basename($_SERVER['PHP_SELF']); /* GET THE CURRENT FILENAME */
?>


<div class="col-12 col-xl-3 filter-column">

    <div class="btn btn-dark btn-ecomm d-xl-none position-fixed top-50 start-0 translate-middle-y"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarFilter"><span><i
                class="bi bi-person me-2"></i>Account</span></div>


    <nav class="navbar navbar-expand-xl flex-wrap p-0">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbarFilter"
            aria-labelledby="offcanvasNavbarFilterLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title mb-0 fw-bold text-uppercase" id="offcanvasNavbarFilterLabel">Account</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body account-menu">
                <div class="list-group w-100 rounded-0">
                    <a href="account-dashboard.php"
                        class="list-group-item <?= ($current_page == 'account-dashboard.php') ? 'active' : '' ?> "><i
                            class="bi bi-house-door me-2"></i>Dashboard</a>

                    <a href="account-orders-1.php"
                        class="list-group-item <?= ($current_page == 'account-orders-1.php') ? 'active' : '' ?>"><i
                            class="bi bi-basket3 me-2"></i>Orders</a>

                    <a href="#" class="list-group-item"><i class="bi bi-suit-heart me-2"></i>Wishlist</a>


                    <a href="account-profile.php"
                        class="list-group-item <?= ($current_page == 'account-profile.php') ? 'active' : '' ?> "><i
                            class="bi bi-person me-2"></i>Profile</a>

                    <a href="account-edit-profile.php"
                        class="list-group-item <?= ($current_page == 'account-edit-profile.php') ? 'active' : '' ?> "><i
                            class="bi bi-pencil me-2"></i>Edit
                        Profile</a>

                    <a href="account-saved-address.php"
                        class="list-group-item <?= ($current_page == 'account-saved-address.php') ? 'active' : '' ?> "><i
                            class="bi bi-pin-map me-2"></i>Saved
                        Address</a>


                    <a href="auth-logout.php" class="list-group-item"><i class="bi bi-power me-2"></i>Logout</a>
                </div>
            </div>
        </div>
    </nav>
</div>