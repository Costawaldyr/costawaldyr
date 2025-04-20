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
        <div class="container">
            <h2>Résultats pour les vols vers <?= htmlspecialchars($destination) ?></h2>
            
            <div class="row">
                <div class="col-md-6">
                    <h3>Vols Aller</h3>
                    <?php if (!empty($volsAller)): ?>
                        <?php foreach ($volsAller as $vol): ?>
                            <div class="card mb-3">
                                <div class="card-header">Vol Aller</div>
                                <div class="card-body">
                                    <h5><?= htmlspecialchars($vol['origine']) ?> → <?= htmlspecialchars($vol['destination']) ?></h5>
                                    <p><i class="fas fa-calendar-day"></i> <?= htmlspecialchars($vol['depart']) ?></p>
                                    <p><i class="fas fa-plane-departure"></i> <?= htmlspecialchars($vol['heure_depart']) ?></p>
                                    <p><i class="fas fa-plane-arrival"></i> <?= htmlspecialchars($vol['heure_arrivee']) ?></p>
                                    <p><i class="fas fa-euro-sign"></i> <?= number_format($vol['prix'], 2, ',', ' ') ?> €</p>
                                    <a href="reservation.php?id=<?= $vol['id'] ?>&type=vols" class="btn btn-primary">Réserver</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun vol aller trouvé.</p>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <h3>Vols Retour</h3>
                    <?php if (!empty($volsRetour)): ?>
                        <?php foreach ($volsRetour as $vol): ?>
                            <div class="card mb-3">
                                <div class="card-header">Vol Retour</div>
                                <div class="card-body">
                                    <h5><?= htmlspecialchars($vol['origine']) ?> → <?= htmlspecialchars($vol['destination']) ?></h5>
                                    <p><i class="fas fa-calendar-day"></i> <?= htmlspecialchars($vol['depart']) ?></p>
                                    <p><i class="fas fa-plane-departure"></i> <?= htmlspecialchars($vol['heure_depart']) ?></p>
                                    <p><i class="fas fa-plane-arrival"></i> <?= htmlspecialchars($vol['heure_arrivee']) ?></p>
                                    <p><i class="fas fa-euro-sign"></i> <?= number_format($vol['prix'], 2, ',', ' ') ?> €</p>
                                    <a href="reservation.php?id=<?= $vol['id'] ?>&type=vols" class="btn btn-primary">Réserver</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun vol retour trouvé.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
