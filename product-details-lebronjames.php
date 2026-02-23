<?php session_start(); $current_page = 'products'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LeBron James | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>
    <div class="page-banner" style="background-image: url('imagefigurine/POPlebronjames.jpg');">
        <div class="container"><div class="text-content"><h2>LeBron James</h2></div></div>
    </div>
    <div class="products theme-main">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="imagefigurine/POPlebronjames.jpg" alt="Lebron James" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>Lebron James</h2>
                    <p><strong class="text-primary">€14.99</strong> <del>€19.99</del></p>
                    <p>Ajoutez une légende du basket à votre collection avec cette figurine de LeBron James.</p>
                    <!-- Formulaire pour ajouter au panier -->
                    <form action="panier.php" method="POST">
                        <input type="hidden" name="product_name" value="Lebron James">
                        <input type="hidden" name="product_price" value="14.99">
                        <input type="hidden" name="product_image" value="imagefigurine/POPlebronjames.jpg">
                        <div class="form-group">
                            <label for="quantity">Quantité</label>
                            <input type="number" id="quantity" name="product_quantity" class="form-control" value="1" min="1">
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>