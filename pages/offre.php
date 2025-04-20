<?php
require_once('../data_base/connection.php');
session_start();

try {
    // Requête SQL avec jointures pour récupérer les offres, destinations et logements
    $sQuery = '
        SELECT 
            offres.*, 
            destinations.pays AS destination_pays, 
            destinations.ville AS destination_ville, 
            logements.nom AS logement_nom, 
            logements.type_logement AS logement_type 
        FROM offres
        LEFT JOIN destinations ON offres.destination_id = destinations.id
        LEFT JOIN logements ON offres.logement_id = logements.id
    ';
    $stmt = $dbh->prepare($sQuery);
    $stmt->execute();
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
catch (PDOException $e) 
{
    die('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
}

// Fonction pour vérifier si une offre est en favoris
function estFavori($dbh, $user_id, $offre_id) {
    $stmt = $dbh->prepare("SELECT id FROM favoris WHERE user_id = ? AND offre_id = ?");
    $stmt->execute([$user_id, $offre_id]);
    return $stmt->fetch() !== false;
}

// Gestion des favoris
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_favori'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    $offre_id = intval($_POST['offre_id']);
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action_favori'];

    if ($action === 'ajouter') {
        try {
            $stmt = $dbh->prepare("INSERT INTO favoris (user_id, offre_id, date_ajout) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $offre_id]);
            $message = "Offre ajoutée aux favoris";
        } catch (PDOException $e) {
            $message = "Cette offre est déjà dans vos favoris";
        }
    } elseif ($action === 'retirer') {
        $stmt = $dbh->prepare("DELETE FROM favoris WHERE user_id = ? AND offre_id = ?");
        $stmt->execute([$user_id, $offre_id]);
        $message = "Offre retirée des favoris";
    }

    // Recharger la page pour voir les changements
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

try {
    // Requête SQL avec jointures pour récupérer les offres, destinations et logements
    $sQuery = '
        SELECT 
            offres.*, 
            destinations.pays AS destination_pays, 
            destinations.ville AS destination_ville, 
            logements.nom AS logement_nom, 
            logements.type_logement AS logement_type 
        FROM offres
        LEFT JOIN destinations ON offres.destination_id = destinations.id
        LEFT JOIN logements ON offres.logement_id = logements.id
    ';
    $stmt = $dbh->prepare($sQuery);
    $stmt->execute();
    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres de voyage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/offres.css">

</head>
<body>
    <header class="header-offres">
        <h1>Nos <span>Offres</span> de Voyage</h1>
    </header>

    <main class="main-container">
        <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
        
        <section class="offres-section">
            <?php if (empty($offres)): ?>
                <div class="no-offers">
                    <i class="fas fa-suitcase"></i>
                    <p>Aucune offre disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="offres-grid">
                    <?php foreach ($offres as $offre): ?>
                        <div class="offer-card">
                            <div class="offer-image">
                                <img src="<?= !empty($offre['images']) ? htmlspecialchars($offre['images']) : '../img/default_offre.jpg'; ?>" alt="<?= htmlspecialchars($offre['titre']) ?>">
                            </div>
                            
                            <div class="offer-content">
                                <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                                
                                <div class="offer-details">
                                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($offre['destination_pays']) ?></span>
                                    <span><i class="fas fa-city"></i> <?= htmlspecialchars($offre['destination_ville']) ?></span>
                                    <span><i class="fas fa-hotel"></i> <?= htmlspecialchars($offre['logement_nom']) ?></span>
                                    <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($offre['date_depart'])) ?></span>
                                </div>

                                <div class="offer-price">
                                    <span class="price-current"><?= number_format($offre['prix'], 2, ',', ' ') ?> €</span>
                                </div>
                                
                                <div class="offer-actions">
                                    <a href="details_offre.php?id=<?= $offre['id'] ?>" class="btn-details">Détails <i class="fas fa-arrow-right"></i></a>
                                    <a href="reservation.php?id=<?= $offre['id'] ?>" class="btn-reserver">Réserver <i class="fas fa-shopping-cart"></i></a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="offre_id" value="<?= $offre['id'] ?>">
                                            <?php if (estFavori($dbh, $_SESSION['user_id'], $offre['id'])): ?>
                                                <input type="hidden" name="action_favori" value="retirer">
                                                <button type="submit" class="btn-favorite" style="color: red;">
                                                    <i class="fas fa-heart"></i> 
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="action_favori" value="ajouter">
                                                <button type="submit" class="btn-favorite">
                                                    <i class="far fa-heart"></i> 
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="logo-footer">
                    <span>Tripster</span>
                    <p>Voyagez jeune, vivez libre</p>
                </div>
                
                <div class="social-links">
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-snapchat"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Explorer</h3>
                <ul>
                    <li><a href="pages/offre.php">Offres spéciales</a></li>
                    <li><a href="pages/blog.php">Blog voyage</a></li>
                    <li><a href="pages/forum.php">Forum</a></li>
                    <li><a href="#">Destinations tendances</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Aide</h3>
                <ul>
                    <li><a href="pages/contact.php">Contactez-nous</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Conditions générales</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Moyens de paiement</h3>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fab fa-cc-paypal" title="PayPal"></i>
                    <i class="fab fa-cc-apple-pay" title="Apple Pay"></i>
                </div>
                
                <div class="app-download">
                    <p>Téléchargez notre app</p>
                    <div class="app-buttons">
                        <a href="#"><img src="img/app-store.png" alt="App Store"></a>
                        <a href="#"><img src="img/google-play.png" alt="Google Play"></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 Tripster. Tous droits réservés. | Conçu avec <i class="fas fa-heart"></i> pour les jeunes voyageurs</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>