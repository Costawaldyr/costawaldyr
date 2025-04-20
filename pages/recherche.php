<?php
require_once('../data_base/connection.php');

// Récupérer les paramètres de recherche
$searchType = $_GET['type'] ?? 'voyages';
$destination = $_GET['destination'] ?? '';
$departure = $_GET['departure'] ?? '';
$returnDate = $_GET['return'] ?? '';
$passengers = $_GET['passengers'] ?? 1;

try {
    if ($searchType === 'voyages') {
        // Recherche d'offres de voyage
        $query = "SELECT o.*, d.ville, d.pays 
                 FROM offres o 
                 JOIN destinations d ON o.destination_id = d.id 
                 WHERE d.ville LIKE :destination 
                 AND o.date_depart >= :departure
                 ORDER BY o.prix ASC";
        
        $stmt = $dbh->prepare($query);
        $stmt->execute([
            ':destination' => "%$destination%",
            ':departure' => $departure
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Afficher les résultats
        include('resultats_voyages.php');
        
    } elseif ($searchType === 'vols') {
        // Recherche de vols
        $queryAller = "SELECT * FROM vols 
                      WHERE destination LIKE :destination 
                      AND depart = :departure
                      ORDER BY prix ASC";
        
        $stmtAller = $dbh->prepare($queryAller);
        $stmtAller->execute([
            ':destination' => "%$destination%",
            ':departure' => $departure
        ]);
        $volsAller = $stmtAller->fetchAll(PDO::FETCH_ASSOC);
        
        $volsRetour = [];
        if (!empty($returnDate)) {
            $queryRetour = "SELECT * FROM vols 
                          WHERE origine LIKE :destination 
                          AND depart = :returnDate
                          ORDER BY prix ASC";
            
            $stmtRetour = $dbh->prepare($queryRetour);
            $stmtRetour->execute([
                ':destination' => "%$destination%",
                ':returnDate' => $returnDate
            ]);
            $volsRetour = $stmtRetour->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Afficher les résultats
        include('resultats_vols.php');
        
    } elseif ($searchType === 'hebergements') {
        // Recherche d'hébergements
        $query = "SELECT l.*, d.ville, d.pays, 
                li.image_url
                 FROM logements l 
                 JOIN destinations d ON l.destination_id = d.id 
                 LEFT JOIN logement_images li ON li.logement_id AND li.ordre = 1
                 WHERE d.ville LIKE :destination 
                 ORDER BY l.prix_nuit ASC";
        
        $stmt = $dbh->prepare($query);
        $stmt->execute([
            ':destination' => "%$destination%"
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Afficher les résultats
        include('resultats_hebergements.php');
    }
} catch(PDOException $e) {
    die('Erreur lors de la recherche : ' . $e->getMessage());
}
?>
