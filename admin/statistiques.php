<?php
require_once('../data_base/connection.php');

// Récupérer le nombre total d'utilisateurs
$sQuery2 = 'SELECT COUNT(*) FROM users';
$stmt2 = $dbh->prepare($sQuery2);
$stmt2->execute();
$total_users = $stmt2->fetchColumn();

// Récupérer le nombre total de réservations
$sQuery3 = "SELECT COUNT(*) FROM reservations";
$stmt3 = $dbh->prepare($sQuery3);
$stmt3->execute();
$total_reservations = $stmt3->fetchColumn();

// Récupérer le nombre total de réservations annulées
$sQueryCanceled = "SELECT COUNT(*) FROM reservations WHERE statut = 'annulé'"; // Assurez-vous que le statut est correct
$stmtCanceled = $dbh->prepare($sQueryCanceled);
$stmtCanceled->execute();
$total_reservations_canceled = $stmtCanceled->fetchColumn();

// Récupérer le total des revenus
$sQuery4 = "SELECT SUM(prix_total) FROM reservations WHERE statut != 'annulé'"; // Exclure les réservations annulées
$stmt4 = $dbh->prepare($sQuery4);
$stmt4->execute();
$total_revenus = $stmt4->fetchColumn();
if ($total_revenus === null) {
    $total_revenus = 0.00;
}

// Récupérer les réservations par mois
$sQuery5 = "SELECT DATE_FORMAT(date_reservation, '%Y-%m') AS mois, COUNT(*) AS total 
    FROM reservations 
    WHERE statut != 'annulé'
    GROUP BY mois 
    ORDER BY mois ASC";
$stmt5 = $dbh->prepare($sQuery5);
$stmt5->execute();
$data = $stmt5->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels = [];
$values = [];
foreach ($data as $row) {
    $labels[] = $row['mois'];
    $values[] = $row['total'];
}

// Récupérer les prix moyens par mois
$sQuery6 = "SELECT DATE_FORMAT(date_reservation, '%Y-%m') AS mois, AVG(prix_total) AS prix_moyen 
    FROM reservations 
    WHERE statut != 'annulé'
    GROUP BY mois 
    ORDER BY mois ASC";
$stmt6 = $dbh->prepare($sQuery6);
$stmt6->execute();
$data_prix = $stmt6->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels_prix = [];
$values_prix = [];
foreach ($data_prix as $row) {
    $labels_prix[] = $row['mois'];
    $values_prix[] = (float) $row['prix_moyen']; // Assurez-vous que c'est un nombre
}

// Récupérer les inscriptions par mois
$sQuery7 = "SELECT DATE_FORMAT(date_inscription, '%Y-%m') AS mois, COUNT(*) AS total 
    FROM users 
    GROUP BY mois 
    ORDER BY mois ASC";
$stmt7 = $dbh->prepare($sQuery7);
$stmt7->execute();
$data_inscriptions = $stmt7->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels_inscriptions = [];
$values_inscriptions = [];
foreach ($data_inscriptions as $row) {
    $labels_inscriptions[] = $row['mois'];
    $values_inscriptions[] = $row['total'];
}

// Récupérer les réservations annulées par mois
$sQueryCanceledByMonth = "SELECT DATE_FORMAT(date_reservation, '%Y-%m') AS mois, COUNT(*) AS total 
    FROM reservations 
    WHERE statut = 'annulé'
    GROUP BY mois 
    ORDER BY mois ASC";
$stmtCanceledByMonth = $dbh->prepare($sQueryCanceledByMonth);
$stmtCanceledByMonth->execute();
$data_canceled = $stmtCanceledByMonth->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels_canceled = [];
$values_canceled = [];
foreach ($data_canceled as $row) {
    $labels_canceled[] = $row['mois'];
    $values_canceled[] = $row['total'];
}

// Récupérer les revenus par mois
$sQueryRevenus = "SELECT DATE_FORMAT(date_reservation, '%Y-%m') AS mois, SUM(prix_total) AS revenus 
    FROM reservations 
    WHERE statut != 'annulé'
    GROUP BY mois 
    ORDER BY mois ASC";
$stmtRevenus = $dbh->prepare($sQueryRevenus);
$stmtRevenus->execute();
$data_revenus = $stmtRevenus->fetchAll(PDO::FETCH_ASSOC);

// Transformer les données pour Chart.js
$labels_revenus = [];
$values_revenus = [];
foreach ($data_revenus as $row) {
    $labels_revenus[] = $row['mois'];
    $values_revenus[] = (float) $row['revenus']; // Assurez-vous que c'est un nombre
}

// Récupérer les statistiques des visiteurs et des clics (exemple fictif)
$total_visitors = 1200; // Exemple
$total_clicks_on_offers = 300; // Exemple
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de Bord - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Tableau de Bord - Vue d’Ensemble</h1>
        <a href="../admin.php" class="btn btn-primary mb-3">Retour</a>
        <div class="row">
            <!-- Utilisateurs inscrits -->
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Utilisateurs inscrits</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_users; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Réservations -->
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Réservations</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_reservations; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Réservations annulées -->
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Réservations annulées</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_reservations_canceled; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Revenus -->
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Revenus générés</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($total_revenus, 2, ',', ' '); ?> €</h5>
                    </div>
                </div>
            </div>

            <!-- Visiteurs -->
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-header">Visiteurs</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_visitors; ?></h5>
                    </div>
                </div>
            </div>

            <!-- Clics sur les offres -->
            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-header">Clics sur les offres</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $total_clicks_on_offers; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="container mt-4">
            <h3 class="text-center">Évolution des Réservations</h3>
            <canvas id="reservationChart"></canvas>
        </div>
        <div class="container mt-4">
            <h3 class="text-center">Évolution des Prix Moyens</h3>
            <canvas id="prixChart"></canvas>
        </div>
        <div class="container mt-4">
            <h3 class="text-center">Évolution des Inscriptions</h3>
            <canvas id="inscriptionsChart"></canvas>
        </div>
        <div class="container mt-4">
            <h3 class="text-center">Réservations Annulées</h3>
            <canvas id="canceledChart"></canvas>
        </div>
        <div class="container mt-4">
            <h3 class="text-center">Évolution des Revenus</h3>
            <canvas id="revenusChart"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Graphique des réservations
            const ctx1 = document.getElementById('reservationChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Réservations par Mois',
                        data: <?php echo json_encode($values); ?>,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(20, 205, 54, 0.39)',
                        fill: true
                    }]
                }
            });

            // Graphique des prix moyens
            const ctx2 = document.getElementById('prixChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels_prix); ?>,
                    datasets: [{
                        label: 'Prix Moyens par Mois',
                        data: <?php echo json_encode($values_prix); ?>,
                        borderColor: 'green',
                        backgroundColor: 'rgba(237, 219, 84, 0.88)',
                        fill: true
                    }]
                }
            });

            // Graphique des inscriptions
            const ctx3 = document.getElementById('inscriptionsChart').getContext('2d');
            new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels_inscriptions); ?>,
                    datasets: [{
                        label: 'Inscriptions par Mois',
                        data: <?php echo json_encode($values_inscriptions); ?>,
                        borderColor: 'purple',
                        backgroundColor: 'rgba(128, 0, 128, 0.2)',
                        fill: true
                    }]
                }
            });

            // Graphique des réservations annulées
            const ctx4 = document.getElementById('canceledChart').getContext('2d');
            new Chart(ctx4, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels_canceled); ?>,
                    datasets: [{
                        label: 'Réservations Annulées par Mois',
                        data: <?php echo json_encode($values_canceled); ?>,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    }]
                }
            });

            // Graphique des revenus
            const ctx5 = document.getElementById('revenusChart').getContext('2d');
            new Chart(ctx5, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels_revenus); ?>,
                    datasets: [{
                        label: 'Revenus par Mois',
                        data: <?php echo json_encode($values_revenus); ?>,
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>