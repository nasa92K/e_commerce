<?php $current_page = 'blog'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Figurine Store | Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .blog-post { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 30px; border: 1px solid rgba(0,0,0,0.08); transition: 0.35s; }
        .blog-post:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(211,47,47,0.18); border-color: #D32F2F; }
        .blog-thumb { overflow: hidden; height: 240px; }
        .blog-thumb img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .blog-post:hover .blog-thumb img { transform: scale(1.05); }
        .blog-post .down-content { padding: 1.5rem; }
        .blog-post .down-content span { color: #D32F2F; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .blog-post .down-content h4, .blog-post .down-content h4 a { font-size: 1.15rem; font-weight: 700; color: #1a1a1a; text-decoration: none !important; }
        .blog-post .down-content h4 a:hover { color: #D32F2F; }
        .blog-post .down-content p { color: #5c5c5c; font-size: 0.9rem; margin-bottom: 1rem; }
        .btn-read { display: inline-block; background: #D32F2F; color: #fff; padding: 0.5rem 1.25rem; border-radius: 8px; font-size: 0.9rem; font-weight: 600; transition: 0.3s; text-decoration: none; }
        .btn-read:hover { background: #B71C1C; color: #fff; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

    <div class="page-banner">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Actualités & Nouveautés</h4>
                        <h2>Le Blog des Collectionneurs</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="products theme-main">
        <div class="container">
            <div class="row">
                
                <div class="col-md-4">
                    <div class="blog-post">
                        <div class="blog-thumb">
                            <img src="imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg" alt="Figurine One Piece">
                        </div>
                        <div class="down-content">
                            <span>Nouveautés</span>
                            <a href="blog-details.php"><h4>Les sorties One Piece 2026</h4></a>
                            <p>Découvrez les nouvelles figurines Gear 5 annoncées pour cette année. Une qualité exceptionnelle au rendez-vous.</p>
                            <a href="blog-details.php" class="btn-read">Lire l'article</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="blog-post">
                        <div class="blog-thumb">
                            <img src="imagefigurine/Naruto.jpg" alt="Figurine Naruto">
                        </div>
                        <div class="down-content">
                            <span>Conseils</span>
                            <a href="blog-details.php"><h4>Bien entretenir ses figurines</h4></a>
                            <p>La poussière et le soleil sont les ennemis de vos pièces de collection. Voici nos astuces d'entretien.</p>
                            <a href="blog-details.php" class="btn-read">Lire l'article</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="blog-post">
                        <div class="blog-thumb">
                            <img src="https://via.placeholder.com/600x400?text=Event+Japan+Expo" alt="Event">
                        </div>
                        <div class="down-content">
                            <span>Événement</span>
                            <a href="blog-details.php"><h4>Retour sur la Japan Expo</h4></a>
                            <p>Un week-end incroyable avec des stands de figurines exclusives. On vous raconte tout notre périple !</p>
                            <a href="blog-details.php" class="btn-read">Lire l'article</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>