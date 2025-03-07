<?php
include 'front-config.php';

?>


<!doctype html>
<html lang="en" class="light-theme">

<head>

  <?php
  include 'includes/style.php';
  ?>

  <title>Shopingo - eCommerce HTML Template</title>
</head>

<body>

  <?php
  include 'includes/headers.php';
  ?>


  <!--start page content-->
  <div class="page-content">

    <!--start carousel-->
    <section class="slider-section">
      <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
            aria-current="true"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></button>

          <!-- <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3"></button>
          <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="4"></button> -->
        </div>


        <div class="carousel-inner">

          <div class="carousel-item active bg-primary">
            <div class="row d-flex align-items-center">

              <div class="col d-none d-lg-flex justify-content-center">
                <div class="">

                  <h3 class="h3 fw-light text-white fw-bold">New Arrival</h3>
                  <h1 class="h1 text-white fw-bold">Women Fashion</h1>
                  <p class="text-white fw-bold"><i>Last call for upto 25%</i></p>
                  <div class=""><a class="btn btn-dark btn-ecomm" href="shop-grid.php">Shop Now</a>
                  </div>

                </div>
              </div>

              <div class="col">
                <img src="assets/images/sliders/s_1.webp" class="img-fluid" alt="...">
              </div>

            </div>
          </div>


          <div class="carousel-item bg-red">
            <div class="row d-flex align-items-center">
              <div class="col d-none d-lg-flex justify-content-center">
                <div class="">
                  <h3 class="h3 fw-light text-white fw-bold">Latest Trending</h3>
                  <h1 class="h1 text-white fw-bold">Fashion Wear</h1>
                  <p class="text-white fw-bold"><i>Last call for upto 35%</i></p>
                  <div class=""> <a class="btn btn-dark btn-ecomm" href="shop-grid.php">Shop Now</a>
                  </div>
                </div>
              </div>
              <div class="col">
                <img src="assets/images/sliders/s_2.webp" class="img-fluid" alt="...">
              </div>
            </div>
          </div>


          <div class="carousel-item bg-purple">
            <div class="row d-flex align-items-center">
              <div class="col d-none d-lg-flex justify-content-center">
                <div class="">
                  <h3 class="h3 fw-light text-white fw-bold">New Trending</h3>
                  <h1 class="h1 text-white fw-bold">Kids Fashion</h1>
                  <p class="text-white fw-bold"><i>Last call for upto 15%</i></p>
                  <div class=""><a class="btn btn-dark btn-ecomm" href="shop-grid.php">Shop Now</a>
                  </div>
                </div>
              </div>
              <div class="col">
                <img src="assets/images/sliders/s_3.webp" class="img-fluid" alt="...">
              </div>
            </div>
          </div>


          <!-- <div class="carousel-item bg-yellow">
            <div class="row d-flex align-items-center">
              <div class="col d-none d-lg-flex justify-content-center">
                <div class="">
                  <h3 class="h3 fw-light text-dark fw-bold">Latest Trending</h3>
                  <h1 class="h1 text-dark fw-bold">Electronics Items</h1>
                  <p class="text-dark fw-bold"><i>Last call for upto 45%</i></p>
                  <div class=""><a class="btn btn-dark btn-ecomm" href="shop-grid.html">Shop Now</a>
                  </div>
                </div>
              </div>
              <div class="col">
                <img src="assets/images/sliders/s_4.webp" class="img-fluid" alt="...">
              </div>
            </div>
          </div> -->


          <!-- <div class="carousel-item bg-green">
            <div class="row d-flex align-items-center">
              <div class="col d-none d-lg-flex justify-content-center">
                <div class="">
                  <h3 class="h3 fw-light text-white fw-bold">Super Deals</h3>
                  <h1 class="h1 text-white fw-bold">Home Furniture</h1>
                  <p class="text-white fw-bold"><i>Last call for upto 24%</i></p>
                  <div class=""><a class="btn btn-dark btn-ecomm" href="shop-grid.html">Shop Now</a>
                  </div>
                </div>
              </div>
              <div class="col">
                <img src="assets/images/sliders/s_5.webp" class="img-fluid" alt="...">
              </div>
            </div>
          </div> -->

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
          data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
          data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>

      </div>
    </section>
    <!--end carousel-->





    <!--start Featured Products slider-->
    <section class="section-padding">
      <div class="container">

        <div class="text-center pb-3">
          <h3 class="mb-0 h3 fw-bold">Featured Products</h3>
          <p class="mb-0 text-capitalize">Buy Featured Products </p>
        </div>

        <div class="product-thumbs">
          <?php
          $product_query = $conn->query(" 
            SELECT 
                p.product_id, 
                p.product_name, 
                mc.main_category_name, 
                sc.sub_category_name,
                p.description, 
                p.is_stock,
                p.image_one 
            FROM 
                product p
            JOIN 
                main_category mc ON p.main_category_id = mc.cid
            JOIN 
                sub_category sc ON p.sub_category_id = sc.sid
            WHERE p.is_featured = 1 ORDER BY rand() LIMIT 10;
        ");

          while ($product = $product_query->fetch_assoc()) {

            $price_query = $conn->query("
                    SELECT combination_id, price 
                    FROM price_combination 
                    WHERE product_id = " . $product['product_id'] . " 
                    ORDER BY RAND() 
                    LIMIT 1
                ");


            if ($price_query->num_rows > 0) {
              $price_data = $price_query->fetch_assoc();
              $product['price'] = $price_data['price'];
              $product['combination_id'] = $price_data['combination_id']; // Store combination_id
            } else {
              $product['price'] = 'N/A';
              $product['combination_id'] = null;
            }




            // $product['price'] = ($price_query->num_rows > 0) ? $price_query->fetch_assoc()['price'] : 'N/A';
            include 'the-product.php';
          }
          ?>
        </div>
      </div>
    </section>
    <!--end Featured Products slider-->



    <?php
    function fetchProducts($condition)
    {
      global $conn;
      $condition = !empty($condition) ? "WHERE " . $condition : "";

      $product_query = $conn->query("
                    SELECT 
                      p.product_id, 
                      p.product_name, 
                      mc.main_category_name,
                      sc.sub_category_name,
                      p.description, 
                      p.is_stock,
                      p.image_one 
                  FROM 
                      product p
                  JOIN 
                      main_category mc ON p.main_category_id = mc.cid
                  JOIN 
                      sub_category sc ON p.sub_category_id = sc.sid
                  $condition
          
      ");

      while ($product = $product_query->fetch_assoc()) {
        $price_query = $conn->query("
              SELECT combination_id, price 
              FROM price_combination 
              WHERE product_id = " . (int) $product['product_id'] . " 
              ORDER BY RAND() 
              LIMIT 1
          ");

        // $product['price'] = ($price_query->num_rows > 0) ? $price_query->fetch_assoc()['price'] : 'N/A';
    

        if ($price_query->num_rows > 0) {
          $price_data = $price_query->fetch_assoc();
          $product['price'] = $price_data['price'];
          $product['combination_id'] = $price_data['combination_id'];
        } else {
          $product['price'] = 'N/A';
          $product['combination_id'] = null;
        }

        include 'the-product.php';
      }
    }
    ?>



    <!--start tabular product-->
    <section class="product-tab-section section-padding bg-light">
      <div class="container">
        <div class="text-center pb-3">
          <h3 class="mb-0 h3 fw-bold">Latest Products</h3>
          <p class="mb-0 text-capitalize">Buy Latest Products </p>
        </div>


        <div class="row">
          <div class="col-auto mx-auto">
            <div class="product-tab-menu table-responsive">
              <ul class="nav nav-pills flex-nowrap" id="pills-tab" role="tablist">

                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#new-arrival" type="button">New
                    Arrival</button>
                </li>

                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="pill" data-bs-target="#best-sellar" type="button">Best
                    Sellar</button>
                </li>

                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="pill" data-bs-target="#trending-product"
                    type="button">Trending</button>
                </li>

                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="pill" data-bs-target="#special-offer" type="button">Special
                    Offer</button>
                </li>

              </ul>
            </div>
          </div>
        </div>

        <hr>

        <!-- <div class="ribban">New Season</div> -->
        <div class="tab-content tabular-product">
          <div class="tab-pane fade show active" id="new-arrival">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-4">

              <?php fetchProducts("1 ORDER BY product_id DESC LIMIT 8"); ?>

            </div>
          </div>


          <div class="tab-pane fade" id="best-sellar">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-4">
              <?php fetchProducts("is_best_seller = 1 ORDER BY rand()  LIMIT 8"); ?>
            </div>
          </div>


          <div class="tab-pane fade" id="trending-product">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-4">
              <?php fetchProducts("is_trending = 1 ORDER BY rand() LIMIT 8"); ?>
            </div>
          </div>

          <div class="tab-pane fade" id="special-offer">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-4">
              <?php fetchProducts("is_under_special_offer = 1 ORDER BY rand() LIMIT 8"); ?>
            </div>
          </div>
        </div>

      </div>
    </section>
    <!--end tabular product-->





    <!--start features-->
    <section class="product-thumb-slider section-padding">
      <div class="container">

        <div class="text-center pb-3">
          <h3 class="mb-0 h3 fw-bold">Why Shop With Us?</h3>
          <p class="mb-0 text-capitalize">Bringing you the best in fashion with premium services</p>
        </div>

        <div class="row row-cols-1 row-cols-lg-4 g-4">

          <div class="col d-flex">
            <div class="card depth border-0 rounded-0 border-bottom border-primary border-3 w-100">
              <div class="card-body text-center">

                <div class="h1 fw-bold my-2 text-primary">
                  <i class="bi bi-truck"></i>
                </div>

                <h5 class="fw-bold">Fast & Free Shipping</h5>
                <p class="mb-0">Enjoy quick and complimentary delivery on all your fashion essentials.</p>
              </div>
            </div>
          </div>


          <div class="col d-flex">
            <div class="card depth border-0 rounded-0 border-bottom border-danger border-3 w-100">
              <div class="card-body text-center">

                <div class="h1 fw-bold my-2 text-danger">
                  <i class="bi bi-credit-card"></i>
                </div>

                <h5 class="fw-bold">Secure & Easy Payments</h5>
                <p class="mb-0">Shop with confidence using our safe and flexible payment options.</p>
              </div>
            </div>
          </div>


          <div class="col d-flex">
            <div class="card depth border-0 rounded-0 border-bottom border-success border-3 w-100">
              <div class="card-body text-center">

                <div class="h1 fw-bold my-2 text-success">
                  <i class="bi bi-arrow-repeat"></i>
                </div>

                <h5 class="fw-bold">Hassle-Free Returns</h5>
                <p class="mb-0">Not satisfied? Return your items easily with our flexible policy.</p>
              </div>
            </div>
          </div>


          <div class="col d-flex">
            <div class="card depth border-0 rounded-0 border-bottom border-warning border-3 w-100">
              <div class="card-body text-center">

                <div class="h1 fw-bold my-2 text-warning">
                  <i class="bi bi-headset"></i>
                </div>

                <h5 class="fw-bold">24/7 Customer Support</h5>
                <p class="mb-0">Our fashion experts are always here to assist you anytime, anywhere.</p>
              </div>
            </div>
          </div>

        </div>
        <!--end row-->
      </div>
    </section>
    <!--end features-->





    <!--start Brands-->
    <section class="section-padding">
      <div class="container">

        <div class="text-center pb-3">
          <h3 class="mb-0 h3 fw-bold">Shop By Brands</h3>
          <p class="mb-0 text-capitalize">Select your favorite brands and purchase</p>
        </div>


        <div class="brands">
          <div class="row row-cols-2 row-cols-lg-5 g-4">

            <?php
            $sql = "SELECT brand_name, brand_image, brand_url FROM brands LIMIT 10";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              while ($brand = $result->fetch_assoc()) { ?>

                <div class="col">
                  <div class="p-3 border rounded brand-box">
                    <div class="d-flex align-items-center">

                      <a href="<?php echo htmlspecialchars($brand['brand_url']); ?>">
                        <img src="backend/<?php echo htmlspecialchars($brand['brand_image']); ?>" class="img-fluid"
                          alt="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                      </a>

                    </div>
                  </div>
                </div>

                <?php
              }
            } else {
              echo "<p class='text-center'>No Brands Available</p>";
            }
            ?>

          </div>
          <!--end row-->
        </div>
      </div>
    </section>
    <!--end Brands-->




    <!--start cartegory slider-->
    <section class="cartegory-slider section-padding bg-section-2">
      <div class="container">

        <div class="text-center pb-3">
          <h3 class="mb-0 h3 fw-bold">Top Categories</h3>
          <p class="mb-0 text-capitalize">Select your favorite categories and purchase</p>
        </div>


        <div class="cartegory-box">
          <?php

          $sql = "SELECT mc.cid, mc.main_category_name, mc.main_category_url, mc.main_category_image, 
                     COUNT(p.product_id) AS product_count
              FROM main_category mc
              LEFT JOIN product p ON mc.cid = p.main_category_id
              GROUP BY mc.cid
              LIMIT 10";


          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) { ?>

              <a href="shop-grid.php?main_category_url=<?php echo $category['main_category_url']; ?>"
                style="text-decoration: none;">


                <div class="card">
                  <div class="card-body">
                    <div class="overflow-hidden">

                      <img src="backend/<?php echo htmlspecialchars($category['main_category_image']); ?>"
                        class="card-img-top rounded-0"
                        alt="<?php echo htmlspecialchars($category['main_category_name']); ?>">

                    </div>

                    <div class="text-center">

                      <h5 class="mb-1 cartegory-name mt-3 fw-bold">
                        <?php echo htmlspecialchars($category['main_category_name']); ?>
                      </h5>
                      <h6 class="mb-0 product-number fw-bold"><?php echo $category['product_count']; ?> Products
                      </h6>

                    </div>
                  </div>
                </div>
              </a>

            <?php }
          } else {
            echo "<p class='text-center'>No categories available</p>";
          }
          $conn->close();
          ?>

        </div>
      </div>
    </section>
    <!--end cartegory slider-->





    <!--subscribe banner-->
    <section class="product-thumb-slider subscribe-banner p-5">
      <div class="row">
        <div class="col-12 col-lg-6 mx-auto">
          <div class="text-center">
            <h3 class="mb-0 fw-bold text-white">Get Latest Update by <br> Subscribe Our Newslater</h3>
            <div class="mt-3">
              <input type="text" class="form-control form-control-lg bubscribe-control rounded-0 px-5 py-3"
                placeholder="Enter your email">
            </div>
            <div class="mt-3 d-grid">
              <button type="button" class="btn btn-lg btn-ecomm bubscribe-button px-5 py-3">Subscribe</button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!--subscribe banner-->


    <!--start blog-->
    <section class="section-padding">
      <div class="container">

        <div class="text-center pb-3">
          <h3 class="mb-0 fw-bold">Latest Blog</h3>
          <p class="mb-0 text-capitalize">Check our latest news</p>
        </div>

        <div class="blog-cards">
          <div class="row row-cols-1 row-cols-lg-3 g-4">

            <div class="col">
              <div class="card">
                <img src="assets/images/blog/01.webp" class="card-img-top rounded-0" alt="...">

                <div class="card-body">

                  <div class="d-flex align-items-center gap-4">
                    <div class="posted-by">
                      <p class="mb-0"><i class="bi bi-person me-2"></i>Virendra</p>
                    </div>
                    <div class="posted-date">
                      <p class="mb-0"><i class="bi bi-calendar me-2"></i>15 Aug, 2022</p>
                    </div>
                  </div>

                  <h5 class="card-title fw-bold mt-3">Spring Fashion Trends 2025</h5>
                  <p class="mb-0">Discover the hottest colors, patterns, and styles taking over the runways this spring.
                    Stay ahead of the trends with our curated collection!</p>
                  <a href="#" class="btn btn-outline-dark btn-ecomm mt-3">Read More</a>

                </div>
              </div>
            </div>


            <div class="col">
              <div class="card">
                <img src="assets/images/blog/02.webp" class="card-img-top rounded-0" alt="...">
                <div class="card-body">
                  <div class="d-flex align-items-center gap-4">
                    <div class="posted-by">
                      <p class="mb-0"><i class="bi bi-person me-2"></i>Virendra</p>
                    </div>
                    <div class="posted-date">
                      <p class="mb-0"><i class="bi bi-calendar me-2"></i>15 Aug, 2022</p>
                    </div>
                  </div>
                  <h5 class="card-title fw-bold mt-3">The Ultimate Guide to Summer Footwear</h5>
                  <p class="mb-0">Step into summer with our guide to comfortable and stylish footwear. Explore sandals,
                    sneakers, and more for every adventure.</p>
                  <a href="#" class="btn btn-outline-dark btn-ecomm mt-3">Read More</a>
                </div>
              </div>
            </div>


            <div class="col">
              <div class="card">
                <img src="assets/images/blog/03.webp" class="card-img-top rounded-0" alt="...">
                <div class="card-body">
                  <div class="d-flex align-items-center gap-4">
                    <div class="posted-by">
                      <p class="mb-0"><i class="bi bi-person me-2"></i>Virendra</p>
                    </div>
                    <div class="posted-date">
                      <p class="mb-0"><i class="bi bi-calendar me-2"></i>15 Aug, 2022</p>
                    </div>
                  </div>
                  <h5 class="card-title fw-bold mt-3">Fashion Hacks: Revamp Your Wardrobe on a Budget</h5>
                  <p class="mb-0">Discover creative fashion hacks to transform old outfits into fresh looks without
                    breaking the bank. Fashionable and budget-friendly!</p>
                  <a href="#" class="btn btn-outline-dark btn-ecomm mt-3">Read More</a>
                </div>
              </div>
            </div>

          </div>
          <!--end row-->
        </div>
      </div>
    </section>
    <!--end blog-->


  </div>
  <!--end page content-->


  <?php
  include 'includes/footers.php';
  ?>









</body>

</html>