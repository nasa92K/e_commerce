<?php
// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$mot_de_passe = "";
$base_de_donnees = "figurine_db";

try {
    $pdo = new PDO("mysql:host=$serveur;dbname=$base_de_donnees;charset=utf8", $utilisateur, $mot_de_passe);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des produits actifs
$sql = "SELECT * FROM produits WHERE actif = 1 ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/favicon.ico">

    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <title>Figurine.com | Vente de figurine en ligne</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

  </head>

  <body>

    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>  
    <!-- ***** Preloader End ***** -->

    <!-- Header -->
    <header class="">
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index.php"><h2>Boutique de Figurine <em>Website</em></h2></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil
                      <span class="sr-only">(current)</span>
                    </a>
                </li> 

                <li class="nav-item active"><a class="nav-link" href="products.php">Produits</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Plus</a>
                    
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="about-us.php">A propos</a>
                      <a class="dropdown-item" href="blog.php">Blog</a>
                      <a class="dropdown-item" href="testimonials.php">Utlisateurs</a>
                      <a class="dropdown-item" href="terms.php">information</a>
                    </div>
                </li>
                
                <li class="nav-item"><a class="nav-link" href="checkout.php">Commande</a></li>

                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <!-- Page Content -->
    <div class="page-heading about-heading header-text" style="background-image: url(imagefigurine/salonfigurine.jpg);">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="text-content">
              <h4>Prochainement en vente</h4>
              <h2>Produits</h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="products">
      <div class="container">
        <div class="row">
          <?php if (!empty($produits)): ?>
            <?php foreach ($produits as $produit): ?>
            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/<?php echo htmlspecialchars($produit['page_details']); ?>">
                  <img src="<?php echo htmlspecialchars($produit['image_url']); ?>" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                </a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/<?php echo htmlspecialchars($produit['page_details']); ?>">
                    <h4><?php echo htmlspecialchars($produit['nom']); ?></h4>
                  </a>
                  <h6>
                    <?php if ($produit['prix_promotion'] && $produit['prix_promotion'] < $produit['prix_original']): ?>
                      <small><del><?php echo number_format($produit['prix_original'], 2); ?>€</del></small> 
                      <?php echo number_format($produit['prix_promotion'], 2); ?>€
                    <?php else: ?>
                      <?php echo number_format($produit['prix_original'], 2); ?>€
                    <?php endif; ?>
                  </h6>
                  <p><?php echo htmlspecialchars($produit['description']); ?></p>
                  
                  <!-- Bouton pour ajouter au panier -->
                  <div class="product-actions" style="margin-top: 15px;">
                    <?php if ($produit['stock_disponible'] && $produit['stock_quantite'] > 0): ?>
                      <button onclick="ajouterAuPanier(<?php echo $produit['id']; ?>)" class="btn btn-primary btn-sm" style="margin-bottom: 5px;">
                        Ajouter au panier
                      </button>
                      <br><small class="text-success">En stock (<?php echo $produit['stock_quantite']; ?> disponible(s))</small>
                    <?php else: ?>
                      <button disabled class="btn btn-secondary btn-sm">Rupture de stock</button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback vers tes produits statiques si la table est vide -->
            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-ghostface.php"><img src="imagefigurine/POPghostface.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-ghostface.php"><h4>POP Ghost Face</h4></a>
                  <h6><small><del>19.99€</del></small> 14.99€</h6>
                  <p>Figure emblématique du cinéma horreur Ghost Face du film Scream sera bientôt en stock</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-leonkennedy.php"><img src="imagefigurine/POPleonkennedy.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-leonkennedy.php"><h4>POP Leon S.Kennedy</h4></a>
                  <h6><small><del>19.99€</del></small> 14.99€</h6>
                  <p>Protagoniste du jeu vidéo Resident Evil, Leon Kennedy arrive bientôt dans nos stocks</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-haerin.php"><img src="imagefigurine/POPhaerin.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-haerin.php"><h4>POP Haerin</h4></a>
                  <h6><small><del>24.99€</del></small> 19.99€</h6>
                  <p>Kang Haerin est une chanteuse K-Pop du célèbre groupe NewJeans, bientôt disponible dans nos stocks dès la sortie officielle en mars</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-squidgame.php"><img src="imagefigurine/POPsquidgame.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-squidgame.php"><h4>Figurine Squid Game</h4></a>
                  <h6><small><del>24.99€</del></small> 19.99€</h6>
                  <p>La figurine Squid Game Garde Masque est une réplique minutieusement détaillée des redoutables gardes du jeu, capturant l'intensité et l'aura mystérieuse de la série</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-mercrediaddams.php"><img src="imagefigurine/POPwednesday.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-mercrediaddams.php"><h4>Figurine Mercredi Addams</h4></a>
                  <h6><small><del>19.99€</del></small> 14.99€</h6>
                  <p>Plongez dans l'univers sombre et fascinant de la famille Addams avec cette superbe figurine de Mercredi Addams. Cette pièce de collection est parfaite pour les amateurs de l'univers gothique et du célèbre personnage.</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="product-item">
                <a href="http://localhost/E-commerceFigurine/product-details-lebronjames.php"><img src="imagefigurine/POPlebronjames.jpg" alt=""></a>
                <div class="down-content">
                  <a href="http://localhost/E-commerceFigurine/product-details-lebronjames.php"><h4>Figurine Lebron James</h4></a>
                  <h6><small><del>19.99€</del></small> 14.99€</h6>
                  <p>Ajoutez une légende du basket à votre collection avec cette superbe figurine de LeBron James. Représentant le King en pleine action, avec son maillot emblématique et ses détails fidèles, cette pièce est parfaite pour les passionnés de NBA et les amateurs de sport.</p>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <div class="col-md-12">
            <ul class="pages">
              <li><a href="#">1</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="inner-content">
              <p>Contact © 2025 Company Name - WEBSITE by Anas Fekhar: <a href="https://manga-story.fr/maintenance/">E-commerceFigurine.com</a></p>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>

    <script>
    function ajouterAuPanier(produitId) {
        // Code JavaScript pour ajouter au panier
        fetch('ajouter_panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({produit_id: produitId, quantite: 1})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produit ajouté au panier !');
                // Optionnel : mettre à jour l'affichage du panier
                if (data.nombre_articles) {
                    console.log('Articles dans le panier: ' + data.nombre_articles);
                    console.log('Total panier: ' + data.panier_total);
                }
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'ajout au panier');
        });
    }
    </script>
  </body>

</html>