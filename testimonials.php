<?php $current_page = 'testimonials'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Figurine Store | Avis des utilisateurs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <style>
    .stars { display: flex; }
    .star { font-size: 2rem; color: lightgray; cursor: pointer; transition: color 0.2s; }
    .star.selected, .star.hover { color: gold; }
    .review-card { background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
  </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

  <div class="page-banner" style="background-image: url(imagefigurine/imagefig1.webp);">
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

  <div class="container my-5 theme-main">
    <div class="row">
      <div class="col-md-6">
        <h4>Laisser un avis</h4>
        <form id="reviewForm">
          <div class="mb-3">
            <label for="name" class="form-label">Nom :</label>
            <input type="text" class="form-control" id="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Note :</label>
            <div class="stars" id="starRating">
              <span class="star" data-value="1">★</span>
              <span class="star" data-value="2">★</span>
              <span class="star" data-value="3">★</span>
              <span class="star" data-value="4">★</span>
              <span class="star" data-value="5">★</span>
            </div>
            <input type="hidden" id="rating" required />
          </div>
          <div class="mb-3">
            <label for="comment" class="form-label">Commentaire :</label>
            <textarea class="form-control" id="comment" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
      </div>
      <div class="col-md-6">
        <h4>Avis récents</h4>
        <div id="reviews" class="mt-3"></div>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>
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
      star.addEventListener('mouseover', () => { updateStars(star.getAttribute('data-value'), true); });
      star.addEventListener('mouseout', () => { updateStars(currentRating); });
    });

    function updateStars(rating, hover = false) {
      stars.forEach((s) => {
        s.classList.remove('selected', 'hover');
        if (s.getAttribute('data-value') <= rating) s.classList.add(hover ? 'hover' : 'selected');
      });
    }

    document.getElementById('reviewForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const name = document.getElementById('name').value;
      const comment = document.getElementById('comment').value;
      const rating = ratingInput.value;
      if (!rating) { alert("Veuillez sélectionner une note."); return; }
      const reviewHTML = `<div class="review-card"><h5>${name}</h5><p class="text-warning">${'★'.repeat(rating)}${'☆'.repeat(5 - rating)}</p><p>${comment}</p></div>`;
      document.getElementById('reviews').insertAdjacentHTML('afterbegin', reviewHTML);
      this.reset();
      updateStars(0);
      currentRating = 0;
    });
  </script>
</body>
</html>
