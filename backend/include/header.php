<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'include/db-config.php';


if (!isset($_SESSION['admin_name'])) {
    header("Location: sign-in.php");
    exit();
}


if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: sign-in.php");
    exit();
}

?>

<div id="ebazar-layout" class="theme-blue">
    <!-- sidebar -->
    <div class="sidebar px-4 py-4 py-md-4 me-0">
        <div class="d-flex flex-column h-100">

            <a href="index.php" class="mb-0 brand-icon">
                <span class="logo-icon">
                    <i class="bi bi-bag-check-fill fs-4"></i>
                </span>
                <span class="logo-text">eBazar</span>
            </a>

            <!-- Menu: main ul -->
            <ul class="menu-list flex-grow-1 mt-3">
                <li><a class="m-link active" href="index.php"><i class="icofont-home fs-5"></i>
                        <span>Dashboard</span></a></li>


                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-product" href="#">
                        <i class="icofont-truck-loaded fs-5"></i> <span>Products</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="menu-product">
                        <li><a class="ms-link" href="product-list.php">Product List</a></li>
                        <li><a class="ms-link" href="product-entry-page.php">Product Add</a></li>
                    </ul>
                </li>




                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#categories" href="#">
                        <i class="icofont-chart-flow fs-5"></i> <span>Master Entry</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="categories">
                        <li><a class="ms-link" href="add-main-category.php">Categories</a></li>
                        <li><a class="ms-link" href="add-sub-category.php">Sub Categories</a></li>
                        <li><a class="ms-link" href="add-colour.php">Colours</a></li>
                        <li><a class="ms-link" href="add-size.php">Size</a></li>
                        <li><a class="ms-link" href="add-brand.php">Add Brand</a></li>


                    </ul>
                </li>



                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-order" href="#">
                        <i class="icofont-notepad fs-5"></i> <span>Orders</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="menu-order">
                        <li><a class="ms-link" href="order-list.php">Orders List</a></li>
                        <li><a class="ms-link" href="order-list.php">Order Details</a></li>
                        <li><a class="ms-link" href="order-list.php">Order Invoices</a></li>
                    </ul>
                </li>



                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#customers-info" href="#">
                        <i class="icofont-funky-man fs-5"></i> <span>Customers</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="customers-info">
                        <li><a class="ms-link" href="#">Customers List</a></li>
                        <li><a class="ms-link" href="#">Customers Details</a></li>
                    </ul>
                </li>



                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-sale" href="#">
                        <i class="icofont-sale-discount fs-5"></i> <span>Sales Promotion</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="menu-sale">
                        <li><a class="ms-link" href="add-discount.php">Coupons List</a></li>
                        <li><a class="ms-link" href="add-discount.php">Coupons Add</a></li>
                        <li><a class="ms-link" href="add-discount.php">Coupons Edit</a></li>
                    </ul>
                </li>


                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-inventory" href="#">
                        <i class="icofont-chart-histogram fs-5"></i> <span>Inventory</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="menu-inventory">
                        <li><a class="ms-link" href="inventory.php">Inventory</a></li>
                    </ul>
                </li>


                <li class="collapsed">
                    <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-Componentsone" href="#"><i
                            class="icofont-ui-calculator"></i> <span>Accounts</span> <span
                            class="arrow icofont-rounded-down ms-auto text-end fs-5"></span></a>
                    <!-- Menu: Sub menu ul -->
                    <ul class="sub-menu collapse" id="menu-Componentsone">
                        <li><a class="ms-link" href="#">Invoices </a></li>
                        <li><a class="ms-link" href="#">Expenses </a></li>
                        <li><a class="ms-link" href="#">Salary Slip </a></li>
                        <li><a class="ms-link" href="#">Create Invoice </a></li>
                    </ul>
                </li>



                <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'Super Admin') { ?>
                    <li><a class="m-link" href="add-employee.php"><i class="icofont-focus fs-5"></i> <span>Employee
                                Management</span></a></li>
                <?php } ?>

                <li><a class="m-link" href="company-details.php"><i class="icofont-focus fs-5"></i> <span>Company
                            Details</span></a></li>

            </ul>

            <!-- Menu: menu collepce btn -->
            <button type="button" class="btn btn-link sidebar-mini-btn text-light">
                <span class="ms-2"><i class="icofont-bubble-right"></i></span>
            </button>

        </div>
    </div>




    <!-- main body area -->
    <div class="main px-lg-4 px-md-4">

        <!-- Body: Header -->
        <div class="header">
            <nav class="navbar py-4">
                <div class="container-xxl">

                    <!-- header rightbar icon -->
                    <div class="h-right d-flex align-items-center mr-5 mr-lg-0 order-1 justify-content-end w-100">

                        <div class="dropdown user-profile ml-2 ml-sm-3 d-flex align-items-center zindex-popover">

                            <div class="u-info me-2">
                                <p class="mb-0 text-end line-height-sm "><span
                                        class="font-weight-bold"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : ''; ?></span>
                                </p>
                                <small><?php echo isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : ''; ?>
                                    Profile</small>
                            </div>


                            <a class="nav-link dropdown-toggle pulse p-0" href="#" role="button"
                                data-bs-toggle="dropdown" data-bs-display="static">
                                <img class="avatar lg rounded-circle img-thumbnail" src="images/profile_av.svg"
                                    alt="profile">
                            </a>


                            <div
                                class="dropdown-menu rounded-lg shadow border-0 dropdown-animation dropdown-menu-end p-0 m-0">

                                <div class="card border-0 w280">
                                    <div class="card-body pb-0">

                                        <div class="d-flex py-1">
                                            <img class="avatar rounded-circle" src="images/profile_av.svg"
                                                alt="profile">
                                            <div class="flex-fill ms-3">
                                                <p class="mb-0"><span
                                                        class="font-weight-bold"><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : ''; ?></span>
                                                </p>
                                                <small class="">
                                                    <?php echo isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : ''; ?></small>
                                            </div>
                                        </div>




                                        <div>
                                            <hr class="dropdown-divider border-dark">
                                        </div>
                                    </div>

                                    <div class="list-group m-2 ">
                                        <a href="admin-profile.html"
                                            class="list-group-item list-group-item-action border-0 "><i
                                                class="icofont-ui-user fs-5 me-3"></i>Profile Page</a>

                                        <a href="?logout=true"
                                            class="list-group-item list-group-item-action border-0 "><i
                                                class="icofont-logout fs-5 me-3"></i>Signout</a>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- menu toggler -->
                    <button class="navbar-toggler p-0 border-0 menu-toggle order-3" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mainHeader">
                        <span class="fa fa-bars"></span>
                    </button>


                </div>
            </nav>
        </div>