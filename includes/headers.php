<?php
include 'front-config.php';

include 'includes/style.php';

$companyQuery = "SELECT logo FROM companies WHERE company_id = 1";
$companyQueryresult = mysqli_query($conn, $companyQuery);
$company = mysqli_fetch_assoc($companyQueryresult);
$logoUrl = $company['logo'];


?>


<?php

include 'includes/quick-view.php';
include 'includes/cart-modal.php';


?>



<!--page loader-->
<div class="loader-wrapper">
  <div class="d-flex justify-content-center align-items-center position-absolute top-50 start-50 translate-middle">
    <div class="spinner-border text-dark" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</div>
<!--end loader-->

<!--start top header-->
<header class="top-header">
  <nav class="navbar navbar-expand-xl w-100 navbar-dark container gap-3">


    <a class="navbar-brand d-none d-xl-inline" href="index.php"><img
        src="backend/<?php echo htmlspecialchars($logoUrl); ?>" class="logo-img" alt="Company Logo">
    </a></a>



    <a class="mobile-menu-btn d-inline d-xl-none" href="javascript:;" data-bs-toggle="offcanvas"
      data-bs-target="#offcanvasNavbar">
      <i class="bi bi-list"></i>
    </a>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar">

      <div class="offcanvas-header">
        <div class="offcanvas-logo">
          <img src="backend/<?php echo htmlspecialchars($logoUrl); ?>" class="logo-img" alt="Company Logo">
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>



      <div class="offcanvas-body primary-menu">
        <ul class="navbar-nav justify-content-start flex-grow-1 gap-1">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>









          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
              Categories
            </a>

            <div class="dropdown-menu dropdown-large-menu">
              <div class="row">

                <?php
                $sql = "SELECT * FROM main_category";
                $result = mysqli_query($conn, $sql);
                while ($main = mysqli_fetch_assoc($result)) { ?>

                  <div class="col-12 col-xl-4">
                    <h6 class="large-menu-title">
                      <a href="shop-grid.php?main_category_url=<?php echo $main['main_category_url']; ?>"
                        style="text-decoration: none; color: inherit; font-weight: bold;">
                        <?php echo htmlspecialchars($main['main_category_name']); ?>
                      </a>
                    </h6>



                    <ul class="list-unstyled">
                      <?php
                      $sub_sql = "SELECT * FROM sub_category WHERE cid = " . $main['cid'];
                      $sub_result = mysqli_query($conn, $sub_sql);

                      while ($sub = mysqli_fetch_assoc($sub_result)) { ?>

                        <li>
                          <a href="shop-grid.php?sub_category_url=<?php echo $sub['sub_category_url']; ?>">
                            <?php echo htmlspecialchars($sub['sub_category_name']); ?>
                          </a>
                        </li>

                      <?php } ?>
                    </ul>
                  </div>
                <?php } ?>
              </div>
            </div>
          </li>


          <!-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
              Shop
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Shop Cart</a></li>
              <li><a class="dropdown-item" href="#">Wishlist</a></li>
            </ul>
          </li> -->


          <li class="nav-item">
            <a class="nav-link" href="shop-grid.php">Shop</a>
          </li>


          <li class="nav-item">
            <a class="nav-link" href="#">About</a>
          </li>



          <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
          </li>



          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
              Account
            </a>

            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="account-dashboard.php">Dashboard</a></li>
              <li><a class="dropdown-item" href="account-orders-1.php">My Orders</a></li>
              <li><a class="dropdown-item" href="account-profile.php">My Profile</a></li>
              <li><a class="dropdown-item" href="account-edit-profile.php">Edit Profile</a></li>
              <li><a class="dropdown-item" href="account-saved-address.php">Addresses</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>

              <?php if (isset($_SESSION['user_id'])): ?>
                <li>
                  <Ou class="dropdown-item" href="auth-logout.php">Log Out</Ou>
                </li>

              <?php else: ?>
                <li><a class="dropdown-item" href="auth-login.php">Login</a></li>
                <li><a class="dropdown-item" href="auth-register.php">Register</a></li>
              <?php endif; ?>
            </ul>
          </li>




          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
              Blog
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Blog Post</a></li>
              <li><a class="dropdown-item" href="#">Blog Read</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>





    <ul class="navbar-nav secondary-menu flex-row">
      <li class="nav-item">
        <a class="nav-link dark-mode-icon" href="javascript:;">
          <div class="mode-icon">
            <i class="bi bi-moon"></i>
          </div>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-search"></i></a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="#"><i class="bi bi-suit-heart"></i></a>
      </li>


      <?php
      $cartCount = 0;

      if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        /* $cartQuery = "SELECT SUM(quantity) as total_items FROM cart WHERE customer_id  = '$user_id'"; */
        $cartQuery = "SELECT COUNT(DISTINCT combination_id) as total_items FROM cart WHERE customer_id  = '$user_id'";

        $cartResult = mysqli_query($conn, $cartQuery);
        $cartData = mysqli_fetch_assoc($cartResult);
        $cartCount = $cartData['total_items'] ?? 0;
      } else {
        /* USER IS NOT LOGGED IN, FETCH CART COUNT FROM SESSION */
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
          foreach ($_SESSION['cart'] as $item) {
            /* $cartCount += $item['quantity']; */
            $cartCount = count($_SESSION['cart']);
          }
        }
      }
      ?>


      <li class="nav-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight">
        <a class="nav-link position-relative" href="javascript:;">
          <div class="cart-badge"> <?php echo $cartCount; ?> </div>
          <i class="bi bi-basket2"></i>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="account-dashboard.php"><i class="bi bi-person-circle"></i></a>
      </li>
    </ul>
  </nav>
</header>
<!--end top header-->