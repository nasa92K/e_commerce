<?php
session_start();
require_once __DIR__ . '/config.php';

$base_assets = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$base_assets = ($base_assets === '' || $base_assets === '\\') ? '' : $base_assets . '/';

$message = '';
$messageType = '';

// --- Traitement suppression avis (uniquement l'auteur) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer_avis'])) {
    $id_avis = (int) $_GET['supprimer_avis'];
    if ($id_avis > 0 && isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        try {
            $stmt = $conn->prepare('DELETE FROM avis WHERE id = :id AND user_id = :user_id');
            $stmt->execute([':id' => $id_avis, ':user_id' => $user_id]);
            if ($stmt->rowCount() > 0) {
                $message = 'Votre avis a été supprimé.';
                $messageType = 'success';
            } else {
                $message = 'Avis introuvable ou vous n\'êtes pas autorisé à le supprimer.';
                $messageType = 'warning';
            }
        } catch (PDOException $e) {
            $message = 'Erreur lors de la suppression.';
            $messageType = 'danger';
        }
        header('Location: avis.php?msg=' . urlencode($messageType) . '&txt=' . urlencode($message));
        exit;
    }
}

// --- Récupérer message après redirection (suppression) ---
if (isset($_GET['msg'], $_GET['txt'])) {
    $messageType = preg_replace('/[^a-z]/', '', (string) $_GET['msg']);
    $message = (string) $_GET['txt'];
    if (!in_array($messageType, ['success', 'warning', 'danger', 'info'], true)) $messageType = 'info';
}

// --- Traitement envoi avis (utilisateur connecté uniquement) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_avis'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = 'Vous devez être connecté pour poster un avis.';
        $messageType = 'danger';
    } else {
        $user_id = (int) $_SESSION['user_id'];
        // Stocker le texte brut en UTF-8 (apostrophes, guillemets, accents çàéè...) ; l'échappement XSS se fait à l'affichage
        $message_brut = trim((string) $_POST['message_avis']);
        if (function_exists('mb_substr')) {
            $message_brut = mb_substr($message_brut, 0, 2000, 'UTF-8');
        } else {
            $message_brut = substr($message_brut, 0, 2000);
        }

        if ($message_brut === '') {
            $message = 'Le message ne peut pas être vide.';
            $messageType = 'warning';
        } else {
            try {
                $stmt = $conn->prepare('INSERT INTO avis (user_id, message, date_publication) VALUES (:user_id, :message, NOW())');
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':message' => $message_brut
                ]);
                $message = 'Votre avis a bien été publié.';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message = 'Erreur lors de l\'enregistrement. Vérifiez que la table avis existe (id, user_id, message, date_publication).';
                $messageType = 'danger';
            }
        }
    }
}

// --- Récupération des avis avec JOIN users (nom + avatar) ---
$avisListe = [];
try {
    $stmt = $conn->prepare('
        SELECT a.id, a.message, a.date_publication, a.user_id,
               u.username, u.avatar
        FROM avis a
        INNER JOIN users u ON u.id = a.user_id
        ORDER BY a.date_publication DESC
    ');
    $stmt->execute();
    $avisListe = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Table absente ou structure différente : on affiche juste la liste vide
    $avisListe = [];
}

$defaultAvatarPath = $base_assets . 'assets/img/default-avatar.png';
if (!file_exists(__DIR__ . '/assets/img/default-avatar.png')) {
    $defaultAvatarPath = 'https://bootdey.com/img/Content/avatar/avatar7.png';
}

function avatarAuteur($row, $base_assets, $defaultAvatarPath) {
    $path = isset($row['avatar']) ? trim((string) $row['avatar']) : '';
    if ($path !== '' && file_exists(__DIR__ . '/' . $path)) {
        return $base_assets . $path;
    }
    return $defaultAvatarPath;
}

$current_page = 'avis';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avis | Figurine Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        .avis-card { background: var(--bs-dark); border: 2px solid #D32F2F; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .avis-card .avatar-avis { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #D32F2F; }
        .avis-card .author-name { font-weight: 700; color: #fff; }
        .avis-card .date-avis { color: rgba(255,255,255,0.6); font-size: 0.875rem; }
        .avis-card .message-avis { color: #e0e0e0; margin-top: 0.5rem; margin-bottom: 0; }
        .avis-empty { background: var(--bs-dark); border: 2px solid #D32F2F; border-radius: 12px; padding: 2rem; text-align: center; color: #fff; }
        .avis-card .btn-supprimer-avis { font-size: 0.8rem; padding: 0.25rem 0.5rem; color: #ff6b6b; border-color: #ff6b6b; }
        .avis-card .btn-supprimer-avis:hover { background: #ff6b6b; color: #fff; }
    </style>
</head>
<body class="theme-figurine">
<?php include 'includes/header.php'; ?>

<div class="page-banner theme-main" style="padding-top: 5.5rem; padding-bottom: 3rem; background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);">
    <div class="container">
        <div class="text-content">
            <h4 style="color: #D32F2F;">Vos retours comptent</h4>
            <h2 style="color: #fff;">Avis des utilisateurs</h2>
        </div>
    </div>
</div>

<div class="container theme-main py-4">
    <?php if ($message !== ''): ?>
        <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="card bg-dark border border-danger mb-4">
        <div class="card-body">
            <h5 class="card-title text-white mb-3">Laisser un avis</h5>
            <form method="post" action="" accept-charset="UTF-8">
                <div class="mb-3">
                    <label for="message_avis" class="form-label text-white">Votre message</label>
                    <textarea class="form-control bg-secondary text-white border-danger" id="message_avis" name="message_avis" rows="4" maxlength="2000" placeholder="Partagez votre expérience..."></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Publier l'avis</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <p class="text-white mb-4">Connectez-vous pour laisser un avis.</p>
    <?php endif; ?>

    <h5 class="text-white mb-3">Tous les avis</h5>
    <?php if (count($avisListe) === 0): ?>
        <div class="avis-empty">
            Soyez le premier à donner votre avis !
        </div>
    <?php else: ?>
        <?php foreach ($avisListe as $avis):
            $is_author = isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) ($avis['user_id'] ?? 0);
            $avis_id = (int) ($avis['id'] ?? 0);
        ?>
            <div class="avis-card">
                <div class="d-flex align-items-start gap-3">
                    <img src="<?php echo htmlspecialchars(avatarAuteur($avis, $base_assets, $defaultAvatarPath)); ?>?t=<?php echo time(); ?>" alt="" class="avatar-avis flex-shrink-0">
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <span class="author-name"><?php echo htmlspecialchars($avis['username'] ?? 'Utilisateur'); ?></span>
                                <span class="date-avis ms-2"><?php echo isset($avis['date_publication']) ? date('d/m/Y à H:i', strtotime($avis['date_publication'])) : ''; ?></span>
                            </div>
                            <?php if ($is_author && $avis_id > 0): ?>
                                <a href="avis.php?supprimer_avis=<?php echo $avis_id; ?>" class="btn btn-outline-danger btn-sm btn-supprimer-avis" onclick="return confirm('Supprimer cet avis ?');" title="Supprimer mon avis"><i class="fa fa-trash"></i> Supprimer</a>
                            <?php endif; ?>
                        </div>
                        <p class="message-avis"><?php echo nl2br(htmlspecialchars($avis['message'] ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
