<?php
session_start();
$current_page = 'blog';
$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

// Données article (à brancher sur BDD plus tard)
$article = [
    'titre' => "L'Art de la Collection de Figurines",
    'date' => '16 Fév 2026',
    'auteur' => 'Anas Fekhar',
    'auteur_avatar' => $base_assets . 'imagefigurine/Portrait Pro - Bureau.png',
    'categorie' => 'Passion & Conseils',
    'hero_image' => $base_assets . 'imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg',
];

// Figurines mentionnées (mini cartes sidebar)
$figurines_article = [
    ['nom' => 'Luffy Gear 5', 'image' => $base_assets . 'imagefigurine/figurine-luffy-gear-4th-one-piece-906.jpg', 'lien' => 'products.php', 'prix' => '169,99 €'],
    ['nom' => 'Malenia Elden Ring', 'image' => $base_assets . 'imagefigurine/malenia.jpg', 'lien' => 'products.php', 'prix' => '189,99 €'],
    ['nom' => 'Goku Ultra Instinct', 'image' => $base_assets . 'imagefigurine/product_figures_dragon-ball_blood-of-saiyans_goku_ultra-instinct.webp', 'lien' => 'products.php', 'prix' => '99,99 €'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($article['titre']); ?> | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        :root { --rouge: #D32F2F; --rouge-fonce: #B71C1C; --dark: #1a1a1a; --dark-soft: #2d2d2d; }
        body.blog-detail-page { background: #0d0d0d; color: #e0e0e0; padding-top: 0; }
        body.blog-detail-page .theme-main { padding-top: 5rem; }

        /* Barre de lecture */
        .reading-progress { position: fixed; top: 0; left: 0; height: 4px; background: var(--rouge); z-index: 1040; transition: width 0.15s ease; }

        /* Zone article centrée max 800px */
        .blog-article-wrap { max-width: 800px; margin: 0 auto; }
        .blog-article-wrap .article-body { font-size: 1.1rem; line-height: 1.85; color: #c8c8c8; }
        .blog-article-wrap .article-body p { margin-bottom: 1.25rem; }

        /* H1 avec bordure gauche rouge */
        .blog-title { font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: #fff; border-left: 5px solid var(--rouge); padding-left: 1rem; margin: 2rem 0 1rem; line-height: 1.25; }

        /* Meta badges rouges transparents */
        .meta-badges { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
        .meta-badges .badge-meta { background: rgba(211, 47, 47, 0.25); color: #ff8a80; border: 1px solid rgba(211, 47, 47, 0.5); padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }

        /* Lettrine (drop cap) */
        .article-body .drop-cap::first-letter { float: left; font-size: 3.5rem; line-height: 1; font-weight: 800; color: var(--rouge); margin: 0.1em 0.35rem 0 0; font-family: Georgia, serif; }

        /* Blockquote avec guillemets rouges géants */
        .article-body blockquote { position: relative; border: none; padding: 1.5rem 2rem 1.5rem 3.5rem; margin: 2rem 0; background: rgba(211, 47, 47, 0.08); border-left: 4px solid var(--rouge); border-radius: 0 12px 12px 0; font-style: italic; font-size: 1.15rem; color: #d0d0d0; }
        .article-body blockquote::before { content: '"'; position: absolute; left: 0.75rem; top: -0.25rem; font-size: 4.5rem; color: var(--rouge); opacity: 0.6; font-family: Georgia, serif; line-height: 1; }

        /* Images article rounded-3 + légende */
        .article-body .img-article { border-radius: var(--bs-border-radius-3, 0.5rem); width: 100%; margin: 1.5rem 0; box-shadow: 0 8px 24px rgba(0,0,0,0.35); }
        .article-body figcaption { font-size: 0.85rem; color: #888; margin-top: 0.5rem; margin-bottom: 1rem; }

        /* Sidebar dark */
        .blog-sidebar .widget { background: var(--dark-soft); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.06); }
        .blog-sidebar .widget-title { font-size: 1rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(211, 47, 47, 0.4); }
        .blog-sidebar .mini-card { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0; border-bottom: 1px solid rgba(255,255,255,0.06); text-decoration: none; color: #e0e0e0; transition: background 0.2s; border-radius: 8px; }
        .blog-sidebar .mini-card:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .blog-sidebar .mini-card img { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; }
        .blog-sidebar .mini-card .mini-card-price { font-size: 0.85rem; color: var(--rouge); font-weight: 700; }

        /* Bouton Newsletter punchy */
        .btn-newsletter { background: var(--rouge); color: #fff; border: none; font-weight: 700; padding: 0.85rem 1.5rem; border-radius: 8px; width: 100%; text-transform: uppercase; letter-spacing: 0.05em; transition: background 0.2s, transform 0.2s; }
        .btn-newsletter:hover { background: var(--rouge-fonce); color: #fff; transform: translateY(-2px); }

        /* Commentaires */
        .comments-list { list-style: none; padding: 0; margin: 0; }
        .comments-list .comment-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .comments-list .comment-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(211, 47, 47, 0.4); flex-shrink: 0; }
        .comments-list .comment-author { font-weight: 700; color: #fff; margin-bottom: 0.25rem; }
        .comments-list .comment-date { font-size: 0.8rem; color: #888; }
        .comments-list .comment-text { color: #b8b8b8; margin-top: 0.5rem; line-height: 1.6; }
        .comment-form .form-control, .comment-form .form-select { background: var(--dark-soft); border: 1px solid rgba(255,255,255,0.1); color: #e0e0e0; border-radius: 8px; padding: 0.75rem 1rem; }
        .comment-form .form-control:focus { border-color: var(--rouge); box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25); color: #fff; }
        .comment-form .form-control::placeholder { color: #666; }
        .btn-submit-comment { background: var(--rouge); color: #fff; border: none; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 8px; transition: background 0.2s; }
        .btn-submit-comment:hover { background: var(--rouge-fonce); color: #fff; }

        /* Partage social */
        .share-buttons { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 2rem; }
        .share-buttons .btn-share { width: 44px; height: 44px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.2); color: #c8c8c8; transition: all 0.2s; text-decoration: none; }
        .share-buttons .btn-share:hover { background: var(--rouge); border-color: var(--rouge); color: #fff; }

        /* Auteur sidebar */
        .blog-sidebar .author-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--rouge); margin-bottom: 0.75rem; }
        .blog-sidebar .author-name { font-weight: 700; color: #fff; }
        .blog-sidebar .author-bio { font-size: 0.9rem; color: #999; margin-top: 0.5rem; line-height: 1.5; }
        .blog-sidebar .newsletter-input { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #fff; }
        .blog-sidebar .newsletter-input:focus { border-color: var(--rouge); box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25); }
    </style>
</head>
<body class="theme-figurine blog-detail-page">
<?php include 'includes/header.php'; ?>

<!-- Barre de lecture -->
<div class="reading-progress" id="reading-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>

<div class="container theme-main py-4 py-lg-5">
    <div class="row">
        <div class="col-lg-8">
            <article class="blog-article-wrap">
                <h1 class="blog-title"><?php echo htmlspecialchars($article['titre']); ?></h1>
                <div class="meta-badges">
                    <span class="badge-meta"><i class="fa fa-calendar me-1"></i> <?php echo htmlspecialchars($article['date']); ?></span>
                    <span class="badge-meta"><i class="fa fa-user me-1"></i> <?php echo htmlspecialchars($article['auteur']); ?></span>
                    <span class="badge-meta"><i class="fa fa-tag me-1"></i> <?php echo htmlspecialchars($article['categorie']); ?></span>
                </div>

                <div class="article-body">
                    <p class="lead drop-cap">Bienvenue sur le blog officiel de <strong>Figurine Store</strong>. Aujourd'hui, nous plongeons au cœur de notre passion commune : pourquoi collectionner des figurines est bien plus qu'un simple hobby.</p>

                    <h3 class="h5 text-white mt-4 mb-3">Une passion qui traverse les générations</h3>
                    <p>Depuis la création de notre boutique en 2022 à Paris, nous avons vu défiler des milliers de collectionneurs. De la simple figurine <em>Pop!</em> à la résine 1/4 ultra-détaillée de chez Tsume, chaque pièce raconte une histoire. Pour nous, une figurine n'est pas un morceau de plastique, c'est la cristallisation d'une émotion ressentie devant un anime ou un jeu vidéo.</p>

                    <figure>
                        <img src="<?php echo htmlspecialchars($article['hero_image']); ?>" alt="Figurine One Piece" class="img-article rounded-3">
                        <figcaption>Figurine Luffy Gear 4 — One Piece, édition premium.</figcaption>
                    </figure>

                    <blockquote class="mb-4">Une collection, c'est un musée personnel où chaque objet est un souvenir.</blockquote>

                    <h3 class="h5 text-white mt-4 mb-3">Comment reconnaître une figurine authentique ?</h3>
                    <p>Sur notre site, nous nous engageons à ne vendre que des produits officiels. Voici quelques astuces pour éviter les contrefaçons (bootlegs) :</p>
                    <ul class="mb-4" style="color: #c8c8c8;">
                        <li><strong>Le logo officiel :</strong> Cherchez les logos Toei Animation, Bandai ou Banpresto sur la boîte.</li>
                        <li><strong>La qualité de la peinture :</strong> Les contrefaçons ont souvent des bavures ou des couleurs ternes.</li>
                        <li><strong>Le prix :</strong> Si une figurine valant 150€ est vendue 30€, méfiez-vous !</li>
                    </ul>

                    <h3 class="h5 text-white mt-4 mb-3">Notre engagement envers vous</h3>
                    <p>Chez <strong>Figurine Store</strong>, nous emballons chaque commande avec une protection maximale (papier bulle renforcé) car nous savons à quel point la boîte est importante pour un collectionneur. Notre équipe de passionnés est toujours disponible pour vous conseiller sur vos futurs achats.</p>

                    <!-- Partage social -->
                    <div class="share-buttons">
                        <span class="text-muted me-2 align-self-center small">Partager :</span>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '')); ?>&text=<?php echo urlencode($article['titre']); ?>" target="_blank" rel="noopener" class="btn-share" title="Twitter"><i class="fa fa-twitter"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '')); ?>" target="_blank" rel="noopener" class="btn-share" title="Facebook"><i class="fa fa-facebook"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '')); ?>&title=<?php echo urlencode($article['titre']); ?>" target="_blank" rel="noopener" class="btn-share" title="LinkedIn"><i class="fa fa-linkedin"></i></a>
                        <button type="button" class="btn-share" id="btn-copy-link" title="Copier le lien"><i class="fa fa-link"></i></button>
                    </div>
                </div>
            </article>

            <!-- Commentaires -->
            <section class="blog-article-wrap mt-5 pt-4 border-top border-secondary">
                <h2 class="h4 text-white mb-4">Commentaires</h2>
                <ul class="comments-list">
                    <li class="comment-item">
                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="Avatar" class="comment-avatar">
                        <div>
                            <div class="comment-author">Marie L.</div>
                            <div class="comment-date">15 Fév 2026</div>
                            <p class="comment-text">Super article, ça donne envie de se lancer dans la collection. Merci pour les conseils anti-bootleg !</p>
                        </div>
                    </li>
                    <li class="comment-item">
                        <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="Avatar" class="comment-avatar">
                        <div>
                            <div class="comment-author">Thomas K.</div>
                            <div class="comment-date">14 Fév 2026</div>
                            <p class="comment-text">J’adore la qualité des figurines proposées. Livraison soignée comme décrit.</p>
                        </div>
                    </li>
                </ul>
                <h3 class="h5 text-white mt-5 mb-3">Laisser un commentaire</h3>
                <form class="comment-form" action="#" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="author" class="form-control" placeholder="Votre nom" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Votre email" required>
                        </div>
                        <div class="col-12">
                            <textarea name="comment" class="form-control" rows="5" placeholder="Votre message..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-submit-comment">Envoyer le message</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>

        <aside class="col-lg-4 blog-sidebar">
            <div class="widget">
                <h5 class="widget-title">Figurines mentionnées dans cet article</h5>
                <?php foreach ($figurines_article as $fig): ?>
                <a href="<?php echo htmlspecialchars($fig['lien']); ?>" class="mini-card">
                    <img src="<?php echo htmlspecialchars($fig['image']); ?>" alt="<?php echo htmlspecialchars($fig['nom']); ?>">
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate"><?php echo htmlspecialchars($fig['nom']); ?></div>
                        <div class="mini-card-price"><?php echo htmlspecialchars($fig['prix']); ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="widget text-center">
                <img src="<?php echo htmlspecialchars($article['auteur_avatar']); ?>" alt="<?php echo htmlspecialchars($article['auteur']); ?>" class="author-avatar">
                <div class="author-name"><?php echo htmlspecialchars($article['auteur']); ?></div>
                <p class="text-danger small mb-0">Fondateur & Passionné</p>
                <p class="author-bio">Je partage avec vous ma passion pour l'univers manga et les produits dérivés depuis plus de 5 ans.</p>
                <div class="mt-2">
                    <a href="#" class="btn-share me-1"><i class="fa fa-instagram"></i></a>
                    <a href="#" class="btn-share me-1"><i class="fa fa-twitter"></i></a>
                    <a href="#" class="btn-share"><i class="fa fa-youtube"></i></a>
                </div>
            </div>

            <div class="widget">
                <h5 class="widget-title">S'abonner à la Newsletter</h5>
                <p class="small text-muted mb-3">Recevez nos nouveautés et offres exclusives.</p>
                <form action="#" method="post" class="mb-2">
                    <input type="email" name="newsletter_email" class="form-control mb-2 newsletter-input" placeholder="Votre email" required>
                    <button type="submit" class="btn btn-newsletter">S'abonner</button>
                </form>
            </div>

            <div class="widget">
                <h5 class="widget-title">Catégories</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="blog.php" class="text-decoration-none d-block py-2 text-muted small border-bottom border-secondary">One Piece</a></li>
                    <li><a href="blog.php" class="text-decoration-none d-block py-2 text-muted small border-bottom border-secondary">Naruto Shippuden</a></li>
                    <li><a href="blog.php" class="text-decoration-none d-block py-2 text-muted small border-bottom border-secondary">Dragon Ball Z</a></li>
                    <li><a href="blog.php" class="text-decoration-none d-block py-2 text-muted small border-bottom border-secondary">Jujutsu Kaisen</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var progress = document.getElementById('reading-progress');
    if (!progress) return;
    function updateProgress() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var pct = docHeight > 0 ? Math.min(100, (scrollTop / docHeight) * 100) : 0;
        progress.style.width = pct + '%';
        progress.setAttribute('aria-valuenow', Math.round(pct));
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
})();
document.getElementById('btn-copy-link') && document.getElementById('btn-copy-link').addEventListener('click', function() {
    var url = window.location.href;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() { alert('Lien copié !'); });
    } else {
        var inp = document.createElement('input'); inp.value = url; document.body.appendChild(inp); inp.select(); document.execCommand('copy'); document.body.removeChild(inp); alert('Lien copié !');
    }
});
</script>
</body>
</html>
