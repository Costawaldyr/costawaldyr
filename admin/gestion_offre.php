<?php
require_once('../data_base/connection.php');
require_once('fonctions.php');

// Initialisation des variables
$sBody = '';
$pageEnCours = $_SERVER['PHP_SELF'];

// Gestion de la suppression des offres
if (isset($_POST['supprimer_selection']) && !empty($_POST['offres_a_supprimer'])) 
{
    $ids = implode(',', array_map('intval', $_POST['offres_a_supprimer']));
    try 
    {
        $stmt = $dbh->prepare("DELETE FROM offres WHERE id IN ($ids)");
        $stmt->execute();
        $sBody .= "<div class='alert alert-success'>Les offres sélectionnées ont été supprimées.</div>";
    } 
    catch (PDOException $e) 
    {
        $sBody .= "<div class='alert alert-danger'>Erreur lors de la suppression : " . $e->getMessage() . "</div>";
    }
}

// Gestion des pages

if (isset($_GET['page'])) {
    switch($_GET['page']) {
        case 'showdata':
            $sBody .= showOffre($dbh);
            break;
        
        case 'createdata':
            $sBody .= formCreateOffre($dbh, $pageEnCours);
            break;
            
        case 'importdata':
            $sBody .= formUploadFile($pageEnCours);
            break;
    }
}
else
{
    // Page par défaut
    $sBody .= showOffre($dbh);
}


// Gestion des actions POST
if(isset($_POST['actionPOST'])) 
{
    switch($_POST['actionPOST']) 
    {
        case 'uploadCSV':
            if(isset($_FILES['fichierCSV'])) 
            {
                $aResult = uploadCSV($_FILES['fichierCSV']);
                
                if($aResult['state'] === true)
                {
                    $sBody .= formUpdateOffre('upload/offres.txt', $pageEnCours, $dbh);
                }
                else 
                {
                    $sBody .= "<div class='alert alert-danger'>Problème lors de l'upload du fichier: " . ($aResult['message'] ?? 'Erreur inconnue') . "</div>";
                }
            } 
            else 
            {
                $sBody .= "<div class='alert alert-danger'>Aucun fichier n'a été uploadé.</div>";
            }
            break;
        
            case 'importOffre': 
                if(isset($_POST['data'])) {
                    $sBody .= updateOffre($_POST['data'], $dbh);
                } else {
                    $sBody .= "<div class='alert alert-danger'>Aucune donnée à importer.</div>";
                }
                break;
            
            case 'createOffre':
                $sBody .= createOffre($_POST, $_FILES, $dbh);
                $sBody .= formCreateOffre($dbh, $pageEnCours); 
                break;
    }
}

// Début du HTML
$sDebutHtml = <<<EOT
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des Offres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container mt-4">
EOT;

// Menu de navigation
$sMenu = <<<EOT
        <h1 class="text-center mb-4">Gestion des Offres</h1>
        <a href="../admin.php" class="btn btn-primary mb-3">Retour</a>
        
        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{$pageEnCours}?page=showdata">Voir les offres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{$pageEnCours}?page=createdata">Créer une offre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{$pageEnCours}?page=importdata">Importer des données</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
EOT;

// Fin du HTML
$sFinHtml = <<<EOT
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOT;

// Affichage final
echo $sDebutHtml . $sMenu . $sBody . $sFinHtml;