<?php $current_page = 'products'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Figurine Store | Détails produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>
    <div class="page-banner" style="background-image: url(assets/images/heading-6-1920x500.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>Acheter nos produits en ligne</h4>
              <h2>Détails des Produits</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="products theme-main">
      <div class="container">
        <div class="row">
          <div class="col-md-4 col-xs-12">
            <div>
              <img src="imagefigurine/Capture d'écran 2025-02-17 041239.png" alt="" class="img-fluid wc-image">
            </div>
            <br>
            <div class="row">
              <div class="col-sm-4 col-xs-6">
                <div>
                  <img src="imagefigurine/Capture d'écran 2025-02-17 041250.png" alt="" class="img-fluid">
                </div>
                <br>
              </div>
              <div class="col-sm-4 col-xs-6">
                <div>
                  <img src="imagefigurine/Capture d'écran 2025-02-17 041229.png" alt="" class="img-fluid">
                </div>
                <br>
              </div>
              <div class="col-sm-4 col-xs-6">
                <div>
                  <img src="imagefigurine/Capture d'écran 2025-02-17 041511.png" alt="" class="img-fluid">
                </div>
                <br>
              </div>
            </div>
          </div>

          <div class="col-md-8 col-xs-12">
            <form action="#" method="post" class="form">
              <h2>Figurine Monkey D. Luffy</h2>

              <br>

              <p class="lead">
                <small><del> €499.00 </del></small><strong class="text-primary"> €279.00 </strong>
              </p>

              <br>

              <p class="lead">
              JAW REX Anime/Monkey D. Ruffy Wano Kuni Figurine | (20 cm) Gear 2 Haki
              </p>

              <br> 

              <div class="row">
                <div class="col-sm-4">
                  <label class="control-label">Choix</label>
                  <div class="form-group">
                    <select class="form-control">
                      <option value="0">Taille S</option>
                      <option value="1">Taille M</option>
                      <option value="2">Taille L</option>

                    </select>
                  </div>
                </div>
                <div class="col-sm-8">
                  <label class="control-label">Quantité</label>

                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="1">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <a href="#" class="btn btn-primary btn-block">Add to Cart</a>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="latest-products">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading">
              <h2>Produits similaire</h2>
              <a href="products.php">voir plus <i class="fa fa-angle-right"></i></a>
            </div>
          </div>
          <div class="col-md-4">
            <div class="product-item">
              <a href="product-details.php"><img src="assets/images/figurinebatman.jpg" alt=""></a>
              <div class="down-content">
                <a href="product-details.php"><h4>Batman</h4></a>
                <h6><small><del>€999.00 </del></small> €779.00</h6>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="product-details.php"><img src="imagefigurine/Naruto.jpg" alt=""></a>
              <div class="down-content">
                <a href="product-details.php"><h4>Naruto</h4></a>
                <h6><small><del>€99.00</del></small>  €79.00</h6>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="product-item">
              <a href="product-details.php"><img src="imagefigurine/figurine-harry-potter-mega.jpg" alt=""></a>
              <div class="down-content">
                <a href="product-details.php"><h4>Harry Potter</h4></a>
                <h6><small><del>€999.00</del></small>   €779.00</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Book Now</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="contact-form">
              <form action="#" id="contact">
                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Pick-up location" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Return location" required="">
                          </fieldset>
                       </div>
                  </div>

                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Pick-up date/time" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Return date/time" required="">
                          </fieldset>
                       </div>
                  </div>
                  <input type="text" class="form-control" placeholder="Enter full name" required="">

                  <div class="row">
                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Enter email address" required="">
                          </fieldset>
                       </div>

                       <div class="col-md-6">
                          <fieldset>
                            <input type="text" class="form-control" placeholder="Enter phone" required="">
                          </fieldset>
                       </div>
                  </div>
              </form>
           </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary">Réserver</button>
          </div>
        </div>
      </div>
    </div>

<?php include 'includes/footer.php'; ?>
  </body>
</html>
