<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres de voyage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../styles/offres.css">
    
</head>
<body>
    <div class="container">
        <h2>Résultats pour les hébergements à <?= htmlspecialchars($destination) ?></h2>
        
        <?php if (!empty($results)): ?>
            <div class="row">
                <?php 
                // Affichez seulement le premier résultat
                $logement = reset($results); 
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($logement['image_url'] ?? '../img/default-lodging.jpg') ?>" 
                            class="card-img-top img-fluid lodging-image" 
                            alt="<?= htmlspecialchars($logement['nom'] ?? 'Hébergement sans nom') ?>"
                            loading="lazy">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($logement['nom'] ?? 'Nom non disponible') ?></h5>
                            <p class="card-text flex-grow-1">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= htmlspecialchars($logement['ville'] ?? 'Ville inconnue') ?>, 
                                <?= htmlspecialchars($logement['pays'] ?? 'Pays inconnu') ?><br>
                                <i class="fas fa-home"></i> 
                                Type: <?= htmlspecialchars($logement['type_logement'] ?? 'Non spécifié') ?><br>
                                <i class="fas fa-euro-sign"></i> 
                                <?= isset($logement['prix_nuit']) ? number_format((float)$logement['prix_nuit'], 2, ',', ' ') : '0,00' ?> € par nuit
                            </p>
                            <a href="details_logement.php?id=<?= (int)($logement['id'] ?? 0) ?>" 
                            class="btn btn-primary mt-auto align-self-start">
                                Voir l'hébergement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Aucun hébergement trouvé pour ces critères.
            </div>
        <?php endif; ?>

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
