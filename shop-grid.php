<?php
include 'front-config.php';

?>

<?php


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'filter') {

  $brands = isset($_POST['brands']) ? $_POST['brands'] : [];
  $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
  $subCategories = isset($_POST['subCategories']) ? $_POST['subCategories'] : [];
  $colors = isset($_POST['colors']) ? $_POST['colors'] : [];
  $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : [];
  $priceMin = isset($_POST['priceMin']) ? $_POST['priceMin'] : 0;
  $priceMax = isset($_POST['priceMax']) ? $_POST['priceMax'] : PHP_INT_MAX;




  $sort = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
  $page = isset($_POST['page']) ? (int) $_POST['page'] : 1;

  $products_per_page = 6;
  $offset = ($page - 1) * $products_per_page;

  /*  BUILD THE SQL QUERY BASED ON THE FILTERS */
  $sql = "SELECT p.product_id, p.product_name, p.description, p.image_one, 
                 (SELECT combination_id FROM price_combination pc WHERE pc.product_id = p.product_id ORDER BY RAND() LIMIT 1) AS combination_id,
                 (SELECT  price FROM price_combination pc WHERE pc.product_id = p.product_id ORDER BY RAND() LIMIT 1) AS price
          FROM product p 
          WHERE 1=1";


  /* ADD FILTERS TO THE QUERY */
  if (!empty($brands)) {
    $brands = implode(',', array_map('intval', $brands));
    $sql .= " AND p.brand_id IN ($brands)";
  }

  if (!empty($categories)) {
    $categories = implode(',', array_map('intval', $categories));
    $sql .= " AND p.main_category_id IN ($categories)";
  }

  if (!empty($subCategories)) {
    $subCategories = implode(',', array_map('intval', $subCategories));
    $sql .= " AND p.sub_category_id IN ($subCategories)";
  }



  if (!empty($colors)) {
    $colorConditions = [];

    foreach ($colors as $color) {
      $colorConditions[] = "FIND_IN_SET($color, p.colour_ids)";
    }

    $sql .= " AND (" . implode(' OR ', $colorConditions) . ")";
  }

  /* [ "FIND_IN_SET(1, p.colour_ids)",
        "FIND_IN_SET(2, p.colour_ids)" ] */

  /* FIND_IN_SET(1, p.colour_ids) OR FIND_IN_SET(2, p.colour_ids) */

  if (!empty($sizes)) {
    $sizeConditions = [];
    foreach ($sizes as $size) {
      $sizeConditions[] = "FIND_IN_SET($size, p.size_ids)";
    }
    $sql .= " AND (" . implode(' OR ', $sizeConditions) . ")";
  }



  if ($priceMin !== '' && $priceMax !== '') {
    $sql .= " AND (SELECT price FROM price_combination pc WHERE pc.product_id = p.product_id ORDER BY RAND() LIMIT 1) BETWEEN $priceMin AND $priceMax";
  }

  $sql .= " GROUP BY p.product_id";


  /* ADD SORTING */
  switch ($sort) {
    case '1':
      $sql .= " ORDER BY price DESC";
      break;
    case '2':
      $sql .= " ORDER BY price ASC";
      break;
    default:
      $sql .= " ORDER BY p.product_id DESC"; /* DEFAULT TO NEWEST */
  }


  /* PAGINATION */
  $sql .= " LIMIT $products_per_page OFFSET $offset";

  /*  EXECUTE THE PRODUCT QUERY */
  $product_query = $conn->query($sql);
  $products = [];

  if ($product_query && $product_query->num_rows > 0) {
    while ($row = $product_query->fetch_assoc()) {
      $row['price'] = ($row['price'] !== null) ? $row['price'] : 'N/A';
      $row['combination_id'] = ($row['combination_id'] !== null) ? $row['combination_id'] : null;
      $products[] = $row;
    }
  }

  /* GET TOTAL PRODUCTS COUNT */
  $total_products_query = "SELECT COUNT(DISTINCT p.product_id) as total FROM product p WHERE 1=1";

  /* ADD THE SAME FILTERS TO COUNT THE TOTAL PRODUCTS */
  if (!empty($brands)) {
    $total_products_query .= " AND p.brand_id IN ($brands)";
  }

  if (!empty($categories)) {
    $total_products_query .= " AND p.main_category_id IN ($categories)";
  }

  if (!empty($subCategories)) {
    $total_products_query .= " AND p.sub_category_id IN ($subCategories)";
  }



  if (!empty($colors)) {
    $colorConditions = [];
    foreach ($colors as $color) {
      $colorConditions[] = "FIND_IN_SET($color, p.colour_ids)";
    }
    $total_products_query .= " AND (" . implode(' OR ', $colorConditions) . ")";
  }

  if (!empty($sizes)) {
    $sizeConditions = [];
    foreach ($sizes as $size) {
      $sizeConditions[] = "FIND_IN_SET($size, p.size_ids)";
    }
    $total_products_query .= " AND (" . implode(' OR ', $sizeConditions) . ")";
  }



  if ($priceMin !== '' && $priceMax !== '') {
    $total_products_query .= " AND (SELECT price FROM price_combination pc WHERE pc.product_id = p.product_id ORDER BY RAND() LIMIT 1) BETWEEN $priceMin AND $priceMax";
  }

  /*  EXECUTE THE TOTAL COUNT QUERY */
  $total_products_result = $conn->query($total_products_query);
  $total_products_row = $total_products_result->fetch_assoc();
  $total_products = $total_products_row['total'];

  /* RETURN THE FILTERED PRODUCTS AND TOTAL COUNT AS JSON */
  header('Content-Type: application/json');
  echo json_encode(['products' => $products, 'total' => $total_products]);

  exit();
}

?>

<!doctype html>
<html lang="en">

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


    <!--start breadcrumb-->
    <div class="py-4 border-bottom">
      <div class="container">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:;">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shop With Grid</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->



    <!--start product grid-->
    <section class="section-padding">
      <h5 class="mb-0 fw-bold d-none">Product Grid</h5>

      <div class="container">

        <div class="btn btn-dark btn-ecomm d-xl-none position-fixed top-50 start-0 translate-middle-y"
          data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarFilter"><span><i class="bi bi-funnel me-1"></i>
            Filters</span></div>

        <div class="row">


          <div class="col-12 col-xl-3 filter-column">

            <nav class="navbar navbar-expand-xl flex-wrap p-0">


              <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbarFilter"
                aria-labelledby="offcanvasNavbarFilterLabel">

                <div class="offcanvas-header">
                  <h5 class="offcanvas-title mb-0 fw-bold" id="offcanvasNavbarFilterLabel">Filters</h5>
                  <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
                </div>



                <div class="offcanvas-body">
                  <div class="filter-sidebar">


                    <div class="card rounded-0">
                      <div class="card-header d-none d-xl-block bg-transparent">
                        <h5 class="mb-0 fw-bold">Filters</h5>
                      </div>





                      <div class="card-body">
                        <h6 class="p-1 fw-bold bg-light">Categories</h6>


                        <div class="categories">
                          <?php
                          $mainCategories = [];
                          $sql = "SELECT m.cid, m.main_category_name, m.main_category_url, COUNT(p.product_id) AS product_count
                                  FROM main_category m
                                  LEFT JOIN product p ON m.cid = p.main_category_id
                                  GROUP BY m.cid 
                                  ORDER BY m.main_category_name ASC";

                          $result = $conn->query($sql);
                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              $mainCategories[] = $row;
                            }
                          }



                          ?>
                          <div class="categories-wrapper height-0 p-1">
                            <?php foreach ($mainCategories as $category): ?>

                              <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                  value="<?php echo htmlspecialchars($category['cid']); ?>"
                                  id="chekCate<?php echo htmlspecialchars($category['cid']); ?>" <?php echo isset($_REQUEST['main_category_url']) && $_REQUEST['main_category_url'] == $category['main_category_url'] ? 'checked' : '' ?>>

                                <label class="form-check-label"
                                  for="chekCate<?php echo htmlspecialchars($category['cid']); ?>">

                                  <span><?php echo htmlspecialchars($category['main_category_name']); ?></span>

                                  <span class="product-number">(<?php echo $category['product_count']; ?>)</span>

                                </label>
                              </div>
                            <?php endforeach; ?>

                          </div>
                        </div>




                        <hr>

                        <h6 class="p-1 fw-bold bg-light">Sub Categories</h6>
                        <div class="sub-categories">

                          <?php
                          $subCategories = [];

                          $sql = "SELECT 
                                      s.sid, 
                                      s.sub_category_name,
                                      s.sub_category_url, 
                                      COUNT(p.product_id) AS product_count
                                    FROM sub_category s
                                    LEFT JOIN product p ON s.sid = p.sub_category_id
                                    GROUP BY s.sid
                                    ORDER BY s.sub_category_name ASC";

                          $result = $conn->query($sql);

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              $subCategories[] = $row;
                            }
                          }
                          ?>

                          <div class="categories-wrapper height-0 p-1">
                            <?php foreach ($subCategories as $subcat): ?>

                              <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                  value="<?php echo htmlspecialchars($subcat['sid']); ?>"
                                  id="chekSubCate<?php echo htmlspecialchars($subcat['sid']); ?>" <?php echo isset($_REQUEST['sub_category_url']) && $_REQUEST['sub_category_url'] == $subcat['sub_category_url'] ? 'checked' : '' ?>>

                                <label class="form-check-label"
                                  for="chekSubCate<?php echo htmlspecialchars($subcat['sid']); ?>">
                                  <span><?php echo htmlspecialchars($subcat['sub_category_name']); ?></span>
                                  <span class="product-number">(<?php echo $subcat['product_count']; ?>)</span>
                                </label>

                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>

                        <hr>

                        <div class="brands">
                          <?php
                          $brandsList = [];

                          $sql = "SELECT 
                                        b.brand_id, 
                                        b.brand_name, 
                                        COUNT(p.product_id) AS product_count
                                      FROM brands b
                                      LEFT JOIN product p ON b.brand_id = p.brand_id
                                      GROUP BY b.brand_id
                                      ORDER BY b.brand_name ASC";

                          $result = $conn->query($sql);

                          if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                              $brandsList[] = $row;
                            }
                          }
                          ?>

                          <h6 class="p-1 fw-bold bg-light">Brands</h6>

                          <div class="brands-wrapper height-0 p-1">
                            <?php foreach ($brandsList as $brand): ?>

                              <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                  value="<?php echo htmlspecialchars($brand['brand_id']); ?>"
                                  id="chekBrand<?php echo htmlspecialchars($brand['brand_id']); ?>">

                                <label class="form-check-label"
                                  for="chekBrand<?php echo htmlspecialchars($brand['brand_id']); ?>">
                                  <span><?php echo htmlspecialchars($brand['brand_name']); ?></span>
                                  <span class="product-number">(<?php echo $brand['product_count']; ?>)</span>
                                </label>

                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>

                        <hr>


                        <div class="Price">
                          <h6 class="p-1 fw-bold bg-light">Price</h6>
                          <div class="Price-wrapper p-1">

                            <div class="input-group">
                              <input type="text" class="form-control rounded-0" placeholder="₹10">
                              <span class="input-group-text bg-section-1 border-0">-</span>
                              <input type="text" class="form-control rounded-0" placeholder="₹1,000">
                              <button type="button" class="btn btn-outline-dark rounded-0 ms-2"><i
                                  class="bi bi-chevron-right"></i></button>
                            </div>

                          </div>
                        </div>
                        <hr>





                        <div class="colors">

                          <?php
                          $sql = "SELECT 
                                      c.cid, 
                                      c.colour_name, 
                                      (SELECT COUNT(*) 
                                      FROM product p 
                                      WHERE FIND_IN_SET(c.cid, p.colour_ids)) AS product_count
                                    FROM colour c
                                    ORDER BY c.colour_name ASC";

                          $result = $conn->query($sql);
                          ?>

                          <h6 class="p-1 fw-bold bg-light">Colors</h6>
                          <div class="color-wrapper height-0 p-1">

                            <?php if ($result && $result->num_rows > 0): ?>
                              <?php while ($row = $result->fetch_assoc()): ?>

                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox"
                                    value="<?php echo htmlspecialchars($row['cid']); ?>"
                                    id="chekColor<?php echo htmlspecialchars($row['cid']); ?>">


                                  <label class="form-check-label"
                                    for="chekColor<?php echo htmlspecialchars($row['cid']); ?>">
                                    <span><?php echo htmlspecialchars($row['colour_name']); ?></span>
                                    <span class="product-number">(<?php echo $row['product_count']; ?>)</span>
                                  </label>
                                </div>

                              <?php endwhile; ?>
                            <?php else: ?>
                              <p>No colors available.</p>
                            <?php endif; ?>

                          </div>
                        </div>

                        <hr>


                        <div class="sizes">
                          <?php
                          $sql = "SELECT 
                                      s.sid, 
                                      s.size_name, 
                                      (SELECT COUNT(DISTINCT p.product_id) 
                                      FROM product p 
                                      WHERE FIND_IN_SET(s.sid, p.size_ids)) AS product_count
                                    FROM size s
                                    ORDER BY s.size_name ASC";

                          $result = $conn->query($sql);
                          ?>

                          <h6 class="p-1 fw-bold bg-light">Size's</h6>
                          <div class="sizes-wrapper height-0 p-1">

                            <?php if ($result && $result->num_rows > 0): ?>
                              <?php while ($row = $result->fetch_assoc()): ?>

                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox"
                                    value="<?php echo htmlspecialchars($row['sid']); ?>"
                                    id="chekSize<?php echo htmlspecialchars($row['sid']); ?>">

                                  <label class="form-check-label"
                                    for="chekSize<?php echo htmlspecialchars($row['sid']); ?>">
                                    <span><?php echo htmlspecialchars($row['size_name']); ?></span>
                                    <span class="product-number">(<?php echo $row['product_count']; ?>)</span>
                                  </label>

                                </div>
                              <?php endwhile; ?>
                            <?php else: ?>
                              <p>No sizes available.</p>
                            <?php endif; ?>

                          </div>
                        </div>
                        <hr>

                        <div class="discount">
                          <h6 class="p-1 fw-bold bg-light">Discount Range</h6>
                          <div class="discount-wrapper p-1">
                            <?php
                            for ($i = 10, $option = 1; $i <= 80; $i += 10, $option++):
                              ?>

                              <div class="form-check">
                                <input class="form-check-input" name="exampleRadios" type="radio"
                                  value="option<?php echo $option; ?>" id="chekDisc<?php echo $option; ?>">

                                <label class="form-check-label" for="chekDisc<?php echo $option; ?>">
                                  <?php echo $i; ?>% and Above
                                </label>

                              </div>
                            <?php endfor; ?>

                          </div>
                        </div>




                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </nav>
          </div>






          <div class="col-12 col-xl-9">
            <div class="shop-right-sidebar">

              <div class="card rounded-0">
                <div class="card-body p-2">
                  <div class="d-flex align-items-center justify-content-between bg-light p-2">

                    <div class="product-count">0 Items Found</div>



                    <form>
                      <div class="input-group">
                        <span class="input-group-text bg-transparent rounded-0 border-0">Sort By</span>
                        <select class="form-select rounded-0" name="sortOrder">
                          <option value="newest" selected>What's New</option> <!-- Changed to match PHP default -->
                          <option value="1">Price: High to Low</option>
                          <option value="2">Price: Low to High</option>
                        </select>
                      </div>
                    </form>


                  </div>
                </div>
              </div>





              <div class="product-grid mt-4">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                  <!-- Products will be dynamically loaded here -->

                </div>


              </div>
            </div>

            <hr class="my-4">

            <div class="product-pagination">
              <nav>
                <ul class="pagination justify-content-center">

                  <!-- Pagination will be dynamically loaded here -->

                </ul>
              </nav>
            </div>

          </div>
        </div>
      </div><!--end row-->


  </div>
  </section>
  <!--start product details-->




  </div>
  <!--end page content-->


  <?php
  include 'includes/footers.php';
  ?>

  <script>

    $(document).ready(function () {


      $('.form-check-input').on('change', function () {
        applyFilters();
      });


      $('.Price-wrapper button').on('click', function () {
        applyFilters();
      });


      $('select.form-select').on('change', function () {
        applyFilters();
      });

      applyFilters();




    });



    function applyFilters(page = 1) {

      var selectedCategories = [];
      $('.categories .form-check-input:checked').each(function () {
        selectedCategories.push($(this).val());
      });

      var selectedSubCategories = [];
      $('.sub-categories .form-check-input:checked').each(function () {
        selectedSubCategories.push($(this).val());
      });

      var selectedBrands = [];
      $('.brands .form-check-input:checked').each(function () {
        selectedBrands.push($(this).val());
      });

      var selectedColors = [];
      $('.colors .form-check-input:checked').each(function () {
        selectedColors.push($(this).val());
      });

      var selectedSizes = [];
      $('.sizes .form-check-input:checked').each(function () {
        selectedSizes.push($(this).val());
      });

      var priceMin = $('.Price-wrapper input').first().val();
      var priceMax = $('.Price-wrapper input').last().val();

      var sortOrder = $('select[name="sortOrder"]').val();

      $.ajax({
        url: 'shop-grid.php',
        type: 'POST',
        data: {
          action: 'filter',
          brands: selectedBrands,
          categories: selectedCategories,
          subCategories: selectedSubCategories,
          colors: selectedColors,
          sizes: selectedSizes,
          priceMin: priceMin,
          priceMax: priceMax,
          sort: sortOrder,
          page: page
        },
        success: function (data) {

          console.log(data);

          $('.product-grid .row').empty();
          $('.product-count').text(`${data.total} Items Found`);

          if (data.products.length > 0) {
            $.each(data.products, function (index, product) {
              var productHtml = `
                        <div class="col">
                          <div class="card">

                            <div class="position-relative overflow-hidden">
                              <div class="product-options d-flex align-items-center justify-content-center gap-2 mx-auto position-absolute bottom-0 start-0 end-0">

                             <a href="javascript:;" 
                                  class="wishlistbutton" 
                                  data-product-id="${product.product_id}">
                                  <i class="bi bi-heart"></i>
                            </a>

                            <a href="javascript:;" 
                                  class="cartbutton" 
                                  data-combination-id="${product.combination_id}">
                                  <i class="bi bi-cart-check-fill"></i>
                            </a>



                                <a href="javascript:;" class="quick-view-btn" data-id="${product.product_id}" data-bs-toggle="modal" data-bs-target="#QuickViewModal"><i class="bi bi-zoom-in"></i></a>
                              </div>

                              <a href="product-details.html">
                                <img src="backend/${product.image_one}" class="card-img-top" alt="${product.product_name}">
                              </a>

                            </div>

                            <div class="card-body">

                              <div class="product-info text-center">
                                <h6 class="mb-1 fw-bold product-name">${product.product_name}</h6>

                                <p class="mb-0 h6 fw-bold product-price">
                                  ${product.price !== 'N/A' ? "₹" + parseFloat(product.price).toFixed(2) : "Price Not Available"}
                                </p>

                              </div>

                            </div>
                          </div>
                        </div>
                      `;
              $('.product-grid .row').append(productHtml);
            });


            var totalProducts = data.total;
            var productsPerPage = 6;
            var totalPages = Math.ceil(totalProducts / productsPerPage);


            $('.product-pagination').empty();


            var paginationHtml = '<nav><ul class="pagination justify-content-center">';


            paginationHtml += `<li class="page-item ${page <= 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${page - 1}">Previous</a>
                      </li>`;


            for (var i = 1; i <= totalPages; i++) {
              paginationHtml += `<li class="page-item ${i === page ? 'active' : ''}">
                          <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`;
            }


            paginationHtml += `<li class="page-item ${page >= totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-page="${page + 1}">Next</a>
                      </li>`;

            paginationHtml += '</ul></nav>';

            $('.product-pagination').append(paginationHtml);


            $('.page-link').on('click', function (e) {
              e.preventDefault();

              var page = $(this).data('page');

              applyFilters(page);
            });


          } else {
            $('.product-grid .row').append('<p>No products found matching your criteria.</p>');
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Error during filtering:", textStatus, errorThrown);
        }
      });
    }




  </script>


</body>

</html>