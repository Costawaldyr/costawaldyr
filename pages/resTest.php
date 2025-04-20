<?php
require_once('../data_base/connection.php');
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
}


// Initialiser la variable $offres
$offres = null;

// Récupérer les détails de l'offre sélectionnée
if (isset($_GET['id'])) {
    $offre_id = $_GET['id'];
    try {
        $sQuery = '
            SELECT offres.*, logements.*, destinations.pays, destinations.ville
            FROM offres
            JOIN logements ON offres.logement_id = logements.id
            JOIN destinations ON offres.destination_id = destinations.id
            WHERE offres.id = :id
        ';
        $stmt = $dbh->prepare($sQuery);
        $stmt->bindValue(':id', $_GET['id']);
        $stmt->execute();
        $offres = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$offres) {
            die("Offre non trouvée.");
        }
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des détails de l'offre : " . $e->getMessage());
    }
}


// Traiter la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offre_id = $_POST['offre_id'];
    $user_id = $_SESSION['user_id']; // Utilisation de l'ID de session
    $nbr_personnes = $_POST['nbr_personnes'];
    $prix_unitaire = $offres['prix'];
    $prix_total = $prix_unitaire * $nbr_personnes;
    $caution = $prix_total * 0.2; // 20% de caution

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $date_naissance = $_POST['date_naissance'];
    $methode_paiement = $_POST['methode_paiement'];

    try {
        $stmt = $dbh->prepare('
            INSERT INTO reservations (
                user_id, offre_id, vol_aller_id, vol_retour_id, nbr_personnes,
                prix_unitaire, prix_total, caution, statut, methode_paiement,
                user_nom, user_prenom, user_email, user_telephone,
                user_adresse, user_date_naissance, logement_id
            ) VALUES (
                :user_id, :offre_id, :vol_aller_id, :vol_retour_id, :nbr_personnes,
                :prix_unitaire, :prix_total, :caution, :statut, :methode_paiement,
                :user_nom, :user_prenom, :user_email, :user_telephone,
                :user_adresse, :user_date_naissance, :logement_id
            )
        ');
        
        $stmt->execute([
            'user_id' => $user_id,
            'offre_id' => $offre_id,
            'vol_aller_id' => $offres['vol_aller_id'] ?? null,
            'vol_retour_id' => $offres['vol_retour_id'] ?? null,
            'nbr_personnes' => $nbr_personnes,
            'prix_unitaire' => $prix_unitaire,
            'prix_total' => $prix_total,
            'caution' => $caution,
            'statut' => 'en attente',
            'methode_paiement' => $methode_paiement,
            'user_nom' => $nom,
            'user_prenom' => $prenom,
            'user_email' => $email,
            'user_telephone' => $telephone,
            'user_adresse' => $adresse,
            'user_date_naissance' => $date_naissance,
            'logement_id' => $offres['logement_id'] ?? null
        ]);

        // Redirection vers une page de confirmation
        $_SESSION['reservation_success'] = true;
        header("Location: confirmation_reservation.php?id=" . $offre_id);
        exit();
        
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la réservation : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Détails de l'offre</title>
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Lien vers votre CSS -->
    <link rel="stylesheet" href="../styles/offres.css">
</head>
<body>
    <!-- Header -->
    <header class="header-offres">
        <div class="main-container">
            <h1>Réservation <span>d'offre</span></h1>
        </div>
    </header>

    <!-- Détails de l'offre -->
    <main class="main-container">
    <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
        <section class="detail-section">
            <?php if ($offres): ?>
                <div class="detail-container">
                    <!-- Colonne image -->
                    <div class="detail-gallery">
                        <img src="<?= htmlspecialchars($offres['images']) ?>" alt="<?= htmlspecialchars($offres['titre']) ?>" class="main-image">
                    </div>
                    
                    <!-- Colonne informations -->
                    <div class="detail-info">
                        <h2><?= htmlspecialchars($offres['titre']) ?></h2>
                        
                        <div class="detail-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($offres['ville']) . ', ' . htmlspecialchars($offres['pays']) ?></span>
                            <span><i class="fas fa-home"></i> <?= htmlspecialchars($offres['type_logement']) ?></span>
                            <span><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($offres['date_depart']) ?> - <?= htmlspecialchars($offres['date_retour']) ?></span>
                            <span><i class="fas fa-plane"></i> <?= htmlspecialchars($offres['transport']) ?></span>
                        </div>
                        
                        <div class="detail-description">
                            <p><?= htmlspecialchars($offres['description']) ?></p>
                        </div>
                        
                        <div class="detail-highlights">
                            <div class="highlight-item">
                                <i class="fas fa-plane-departure"></i>
                                <div>
                                    <h4>Vol Aller</h4>
                                    <p>Numéro: <?= htmlspecialchars($offres['numero_vol_aller']) ?></p>
                                </div>
                            </div>
                            <div class="highlight-item">
                                <i class="fas fa-plane-arrival"></i>
                                <div>
                                    <h4>Vol Retour</h4>
                                    <p>Numéro: <?= htmlspecialchars($offres['numero_vol_retour']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="price-section">
                            <div class="price"><?= htmlspecialchars($offres['prix']) ?> €</div>
                            <div class="availability"><i class="fas fa-check-circle"></i> Disponible</div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de réservation -->
                <div class="reservation-form">
                    <h2 class="section-title">Formulaire de réservation</h2>
                    <form action="" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="offre_id" value="<?= htmlspecialchars($offres['id']) ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" required>
                                <div class="invalid-feedback">Veuillez entrer votre nom.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" required>
                                <div class="invalid-feedback">Veuillez entrer votre prénom.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                                <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="text" id="telephone" name="telephone" required>
                                <div class="invalid-feedback">Veuillez entrer votre numéro de téléphone.</div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" required>
                                <div class="invalid-feedback">Veuillez entrer votre adresse.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_naissance">Date de naissance</label>
                                <input type="date" id="date_naissance" name="date_naissance">
                            </div>
                            
                            <div class="form-group">
                                <label for="nbr_personnes">Nombre de personnes</label>
                                <input type="number" id="nbr_personnes" name="nbr_personnes" required>
                                <div class="invalid-feedback">Veuillez entrer le nombre de personnes.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="methode_paiement">Méthode de paiement</label>
                                <select id="methode_paiement" name="methode_paiement" required>
                                    <option value="carte bancaire">Carte bancaire</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="virement">Virement</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une méthode de paiement.</div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-reserver">
                                <i class="fas fa-check-circle"></i> Confirmer la réservation
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="no-offers">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Offre non trouvée</h3>
                    <p>L'offre que vous recherchez n'existe pas ou a été supprimée.</p>
                    <a href="offre.php" class="btn-back">Retour aux offres</a>
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

    <!-- Script de validation -->
    <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Dans votre <script>, ajoutez ceci pour calculer le prix total dynamiquement
        document.getElementById('nbr_personnes').addEventListener('change', function() {
            const prixUnitaire = <?= $offres['prix'] ?? 0 ?>;
            const nbrPersonnes = this.value;
            const prixTotal = prixUnitaire * nbrPersonnes;
            document.querySelector('.price-total').textContent = prixTotal.toFixed(2) + ' €';
        });
    </script>
</body>
</html>