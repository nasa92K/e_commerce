<?php
$current_page = 'contact';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Figurine Store | Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

    <div class="page-banner" style="background-image: url(imagefigurine/salonfigurine.jpg);">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Toutes les informations ici</h4>
                        <h2>Nous Contacter</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="find-us theme-main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Où nous trouver sur place</h2>
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="map">
                        <iframe src="https://maps.google.com/maps?q=48.8590277,2.3495607&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="330px" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="left-content">
                        <h4>Comment nous contacter</h4>
                        <p>Si vous avez besoin de nous contacter vous avez le choix par mail ou directement sur place via la localisation.<br><br>Nous serons à votre disposition pour tout renseignement, vous avez accès à nos différents réseaux ci-dessous.</p>
                        <ul class="social-icons">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            <li><a href="#"><i class="fa fa-behance"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="send-message">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Envoyez-nous un message</h2>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="contact-form">
                        <form id="contact" action="" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <fieldset>
                                        <input name="name" type="text" class="form-control" id="name" placeholder="Nom Prénom" required="">
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <fieldset>
                                        <input name="email" type="text" class="form-control" id="email" placeholder="Adresse E-mail" required="">
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <fieldset>
                                        <input name="subject" type="text" class="form-control" id="subject" placeholder="Objet" required="">
                                    </fieldset>
                                </div>
                                <div class="col-lg-12">
                                    <fieldset>
                                        <textarea name="message" rows="6" class="form-control" id="message" placeholder="Saisissez votre message" required=""></textarea>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12">
                                    <fieldset>
                                        <button type="submit" id="form-submit" class="filled-button">Envoyer</button>
                                    </fieldset>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="imagefigurine/Portrait Pro - Bureau.png" class="img-fluid" alt="Anas Fekhar" style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h5 class="text-center fw-bold mt-3">Anas Fekhar</h5>
                    <p class="text-muted">Fondateur</p>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
