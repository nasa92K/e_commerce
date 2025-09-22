<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Figurine.com | Avis des utilisateurs</title>

  <!-- Bootstrap & Fonts -->
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/owl.css" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

  <style>
    .stars {
      display: flex;
    }

    .star {
      font-size: 2rem;
      color: lightgray;
      cursor: pointer;
      transition: color 0.2s;
    }

    .star.selected,
    .star.hover {
      color: gold;
    }

    .review-card {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
    }
  </style>
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
        <a class="navbar-brand" href="index.php">
          <h2>Boutique de Figurine <em>Website</em></h2>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
          aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <li class="nav-item"><a class="nav-link" href="products.php">Produits</a></li>
            <li class="nav-item dropdown active">
              <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                aria-haspopup="true" aria-expanded="false">Plus</a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="about-us.php">A propos</a>
                <a class="dropdown-item" href="blog.php">Profil</a>
                <a class="dropdown-item active" href="testimonials.php">Avis</a>
                <a class="dropdown-item" href="terms.php">Information</a>
              </div>
            </li>
            <li class="nav-item"><a class="nav-link" href="checkout.php">Commande>
            <li class="nav-item"><a class="nav-link" href="contact.php">Nous contacter</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Banner -->
  <div class="page-heading about-heading header-text" style="background-image: url(imagefigurine/imagefig1.webp);">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="text-content">
            <h4>Vos retours comptent</h4>
            <h2>Avis des utilisateurs</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Avis Section -->
  <div class="container my-5">
    <div class="row">
      <!-- Formulaire d’avis -->
      <div class="col-md-6">
        <h4>Laisser un avis</h4>
        <form id="reviewForm">
          <div class="form-group">
            <label for="name">Nom :</label>
            <input type="text" class="form-control" id="name" required />
          </div>

          <div class="form-group">
            <label>Note :</label>
            <div class="stars" id="starRating">
              <span class="star" data-value="1">★</span>
              <span class="star" data-value="2">★</span>
              <span class="star" data-value="3">★</span>
              <span class="star" data-value="4">★</span>
              <span class="star" data-value="5">★</span>
            </div>
            <input type="hidden" id="rating" required />
          </div>

          <div class="form-group">
            <label for="comment">Commentaire :</label>
            <textarea class="form-control" id="comment" rows="4" required></textarea>
          </div>

          <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
      </div>

      <!-- Liste des avis -->
      <div class="col-md-6">
        <h4>Avis récents</h4>
        <div id="reviews" class="mt-3"></div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="inner-content text-center">
            <p>Copyright © 2025 Figurine.com - 
              Template par <a href="https://www.phpjabbers.com/">PHPJabbers</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/owl.js"></script>

  <script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    let currentRating = 0;

    stars.forEach((star) => {
      star.addEventListener('click', () => {
        currentRating = star.getAttribute('data-value');
        ratingInput.value = currentRating;
        updateStars(currentRating);
      });

      star.addEventListener('mouseover', () => {
        updateStars(star.getAttribute('data-value'), true);
      });

      star.addEventListener('mouseout', () => {
        updateStars(currentRating);
      });
    });

    function updateStars(rating, hover = false) {
      stars.forEach((s) => {
        s.classList.remove('selected', 'hover');
        const val = s.getAttribute('data-value');
        if (val <= rating) {
          s.classList.add(hover ? 'hover' : 'selected');
        }
      });
    }

    document.getElementById('reviewForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const name = document.getElementById('name').value;
      const comment = document.getElementById('comment').value;
      const rating = ratingInput.value;

      if (!rating) {
        alert("Veuillez sélectionner une note.");
        return;
      }

      const reviewHTML = `
        <div class="review-card">
          <h5>${name}</h5>
          <p class="text-warning">${'★'.repeat(rating)}${'☆'.repeat(5 - rating)}</p>
          <p>${comment}</p>
        </div>
      `;

      document.getElementById('reviews').insertAdjacentHTML('afterbegin', reviewHTML);
      this.reset();
      updateStars(0);
      currentRating = 0;
    });
  </script>
</body>

</html>
