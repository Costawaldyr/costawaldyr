<?php


function uploadCSV(&$file)
{
    $retour = array();
    $retour['state'] = true;
    $retour['message'] = 'ras';
    

    $dossier = 'upload/';
    $fichier = basename($file['name']);
    $taille_maxi = 100000;
    $taille = filesize($file['tmp_name']);
    $extensions = array('.csv', '.txt');
    $extension = strrchr($file['name'], '.');

    if (!in_array($extension, $extensions))
    {
        $retour['mssage'] = 'Vous devez uploader un fichier de type txt ou csv';
        $retour['state'] = false ;
    }

    if ($taille>$taille_maxi)
    {
        $retour['message'] = 'Le fichier eest trop gros';
        $retour['state'] = false;
    }

    if ($retour['state'] !== false)
    {
        $fichier = strtr($fichier, 
			  'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
			  'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
        $file['name'] = $fichier;

        if(move_uploaded_file($file['tmp_name'], $dossier . $fichier))
        {
            $retour['state'] = true;
			$retour['message'] =  'Upload effectué avec succès !';
        }
        else
        {
            $retour['state'] = false;
			$retour['message'] = 'Echec de l\'upload !';
        }
    }
    return $retour;

}

function formUploadFile($_pageEnCours)
{
	$formUpload = <<<EOT
		<form method="POST" action="{$_pageEnCours}" enctype="multipart/form-data">
				<!-- On limite le fichier à 100Ko -->
				<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
				Fichier : <input type="file" name="fichierCSV" />
				<input type="hidden" name="actionPOST" value="uploadCSV">
				<input type="submit" value="Envoyer le fichier" />
		</form>
EOT;
	return $formUpload;
}
//#######################################################################################
// showUsers($_dbh)                                                                    #
// Affiche la liste des utilisateurs avec options de suppression                       #
//#######################################################################################
function showUsers($_dbh)
{
    $sTable  = '<form method="post" action="">';
    $sTable .= '<table class="table table-bordered table-striped">';
    $sTable .= '<thead class="table-dark">';
    $sTable .= '    <tr>';
    $sTable .= '        <th><input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)"></th>';
    $sTable .= '        <th>ID</th>';
    $sTable .= '        <th>Nom</th>';
    $sTable .= '        <th>Prénom</th>';
    $sTable .= '        <th>Email</th>';
    $sTable .= '        <th>Genre</th>';
    $sTable .= '        <th>Téléphone</th>';
    $sTable .= '        <th>Statut</th>';
    $sTable .= '        <th>Rôle</th>';
    $sTable .= '    </tr>';
    $sTable .= '</thead>';
    $sTable .= '<tbody>';

    $sQuery = 'SELECT id, nom, prenom, email, gender, telephone, statut, access_level 
               FROM users 
               ORDER BY nom, prenom';
    $stmt = $_dbh->prepare($sQuery);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sTable .= '<tr>';
        $sTable .= '<td><input type="checkbox" name="utilisateurs_a_supprimer[]" value="'.$row['id'].'"></td>';
        $sTable .= '<td>'.$row['id'].'</td>';
        $sTable .= '<td>'.$row['nom'].'</td>';
        $sTable .= '<td>'.$row['prenom'].'</td>';
        $sTable .= '<td>'.$row['email'].'</td>';
        $sTable .= '<td>'.$row['gender'].'</td>';
        $sTable .= '<td>'.$row['telephone'].'</td>';
        $sTable .= '<td>'.$row['statut'].'</td>';
        $sTable .= '<td>'.$row['access_level'].'</td>';
        $sTable .= '</tr>';
    }

    $sTable .= '</tbody>';
    $sTable .= '</table>';
    $sTable .= '<button type="submit" name="supprimer_selection" class="btn btn-danger">Supprimer les utilisateurs sélectionnés</button>';
    $sTable .= '</form>';

    // JavaScript pour le bouton "tout sélectionner"
    $sTable .= '
    <script>
        function toggleCheckboxes(source) {
            let checkboxes = document.querySelectorAll("input[name=\'utilisateurs_a_supprimer[]\']");
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = source.checked;
            });
        }
    </script>';

    return $sTable;
}

//#######################################################################################
// formCreateUser($_dbh, $_pageEnCours)                                                #
// Affiche le formulaire de création d'un nouvel utilisateur                           #
//#######################################################################################
function formCreateUser($_dbh, $_pageEnCours)
{
    $form = <<<EOT
    <form method="POST" action="{$_pageEnCours}" enctype="multipart/form-data">
        <input type="hidden" name="actionPOST" value="createUser">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input type="text" class="form-control" name="prenom" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Genre</label>
                <select class="form-select" name="gender" required>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date de naissance</label>
                <input type="date" class="form-control" name="date_naissance">
            </div>
            <div class="col-md-4">
                <label class="form-label">Téléphone</label>
                <input type="tel" class="form-control" name="telephone">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Adresse</label>
            <textarea class="form-control" name="adresse" rows="2"></textarea>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Statut</label>
                <select class="form-select" name="statut">
                    <option value="actif">Actif</option>
                    <option value="suspendu">Suspendu</option>
                    <option value="désactivé">Désactivé</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Rôle</label>
                <select class="form-select" name="access_level">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Photo de profil</label>
            <input type="file" class="form-control" name="profile_picture" accept="image/*">
        </div>
        
        <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
    </form>
EOT;

    return $form;
}

//#######################################################################################
// createUser($_data, $_files, $_dbh)                                                  #
// Crée un nouvel utilisateur dans la base de données                                   #
//#######################################################################################
function createUser($_data, $_files, $_dbh)
{
    try {
        // Gestion de l'upload de la photo de profil
        $profilePicture = null;
        if (!empty($_files['profile_picture']['name'])) {
            $uploadDir = 'uploads/users/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $profilePicture = uniqid() . '_' . basename($_files['profile_picture']['name']);
            $targetFile = $uploadDir . $profilePicture;
            
            if (!move_uploaded_file($_files['profile_picture']['tmp_name'], $targetFile)) {
                return "<div class='alert alert-danger'>Erreur lors de l'upload de la photo de profil.</div>";
            }
        }

        // Hashage du mot de passe
        $hashedPassword = password_hash($_data['password'], PASSWORD_DEFAULT);

        // Préparation de la requête
        $sQuery = 'INSERT INTO users (nom, prenom, email, password, gender, date_naissance, 
                   adresse, telephone, profile_picture, access_level, statut) 
                   VALUES (:nom, :prenom, :email, :password, :gender, :date_naissance, 
                   :adresse, :telephone, :profile_picture, :access_level, :statut)';
        
        $stmt = $_dbh->prepare($sQuery);
        
        // Exécution de la requête
        $stmt->execute([
            'nom' => $_data['nom'],
            'prenom' => $_data['prenom'],
            'email' => $_data['email'],
            'password' => $hashedPassword,
            'gender' => $_data['gender'],
            'date_naissance' => $_data['date_naissance'] ?: null,
            'adresse' => $_data['adresse'] ?: null,
            'telephone' => $_data['telephone'] ?: null,
            'profile_picture' => $profilePicture,
            'access_level' => $_data['access_level'],
            'statut' => $_data['statut']
        ]);
        
        return "<div class='alert alert-success'>L'utilisateur a été créé avec succès!</div>";
        
    } catch(PDOException $e) {
        return "<div class='alert alert-danger'>Erreur lors de la création de l'utilisateur: " . $e->getMessage() . "</div>";
    }
}




function formUpdate($_nomFichier,$_pageEnCours,$_dbh)
{
	
	$handle = fopen($_nomFichier, 'rb');
	if($handle === false)
	{
		return 'probleme ouverture fichier';
	}

	try
	{
		
		$sPrenom = '';
		$sNom = '';
		$sAdresse = '';
		$sTelephone = '';
        $sEmail = '';
        $sGenre = '';
        $sStatut = '';

		$sQuery0 = 'INSERT INTO users (prenom,nom,adresse,telephone,gender,statut, email) VALUES (:prenom, :nom, :adresse, :telephone, :gender, :email, :statut)';
		$stmt0 = $_dbh->prepare($sQuery0);
		
		$stmt0->bindParam(':prenom', $sPrenom);
		$stmt0->bindParam(':nom', $sNom);
		$stmt0->bindParam(':adresse', $sAdresse);
		$stmt0->bindParam(':email', $sEmail);
        $stmt0->bindParam(':gender', $sGenre);
        $stmt0->bindParam(':statut', $sStatut);
        $stmt0->bindParam(':telephone', $sTelephone);

		
		$sQuery1 = 'SELECT prenom,nom,adresse,telephone FROM users WHERE prenom=:prenom AND nom=:nom';
		$stmt1 = $_dbh->prepare($sQuery1);

		$stmt1->bindParam(':prenom', $sPrenom);
		$stmt1->bindParam(':nom', $sNom);
		
		$iCptAdd = 0;
		$iCptDoublon = 0;


		$sForm = '<form method="POST" action="'.$_pageEnCours.'">';
		$sForm .= '<table class="table table-bordered">';
		$sForm .= '<thead>';
		$sForm .= '	<tr>';
		$sForm .= '		<th>Prénom';
		$sForm .= '		<th>Nom';
		$sForm .= '		<th>Adresse';
		$sForm .= '		<th>Téléphone';
        $sForm .= '		<th>email';
        $sForm .= '     <th>Genre';
        $sForm .='      <th>Statut';
		$sForm .= '		<th>Mettre à jour';
		$sForm .= '<tbody>';

		while($row = fgetcsv($handle, 1000, ':'))
		{
			$sPrenom = $row[0];
			$sNom = $row[1];
			$sAdresse = $row[2];
			$sTelephone = $row[3];
            $sEmail = $row[4];
            $sGenre = $row[5];
            $sStatut = $row[6];
			
			
			$stmt1->execute();
			if($stmt1->rowCount() === 0)
			{
				
				$stmt0->execute();
				$iCptAdd++;
			}
			else
			{
			
				$iCptDoublon++;
				
				$sForm .= '	<tr>';
				$sForm .= '		<td>'.$sPrenom;
				$sForm .= '		<td>'.$sNom;
				$sForm .= '		<td>'.$sAdresse;
				$sForm .= '		<td>'.$sTelephone;
			
				$sForm .= '		<td>new <input type="checkbox" name="data[]" value="'.urlencode(serialize($row)).'">'."\n";

				$row = $stmt1->fetch(PDO::FETCH_ASSOC);
				$sForm .= '	<tr class="oldData">';
				$sForm .= '		<td>'.$row['prenom'];
				$sForm .= '		<td>'.$row['nom'];
				$sForm .= '		<td>'.$row['adresse'];
				$sForm .= '		<td>'.$row['telephone'];
				$sForm .= '		<td>old';
			}
			
		}
		fclose($handle);
		$sForm .= '</table>';
		$sForm .= '<input type="hidden" name="actionPOST" value="importData">';
		$sForm .= '<input type="submit" value="Mettre à jour">';
		$sForm .= '</form>';

		return $iCptAdd.' enregistrement(s) ajouté(s) dans la db<br><b>Il y a '.$iCptDoublon.' enregistrement(s) déjà présent(s) dans la db</b>'.$sForm;
	}
	catch(PDOException $e)
	{
		return 'erreur insertion dans la db : '.$e->getMessage();
	}
}



function update($_enregistrements,$_dbh)
{
	try
	{

		$iCpt=0;
		$sQuery = 'UPDATE users SET adresse = :adresse, telephone=:telephone, email=:email, gender=:gender, statut=:statut WHERE prenom =:prenom AND nom = :nom';
		$stmt = $_dbh->prepare($sQuery);
		
		$sPrenom = '';
		$sNom = '';
		$sAdresse = '';
		$sTelephone = '';
        $sEmail = '';
        $sGenre = '';
        $sStatut = '';
		
		$stmt->bindParam(':prenom', $sPrenom);
		$stmt->bindParam(':nom', $sNom);
		$stmt->bindParam(':adresse', $sAdresse);
		$stmt->bindParam(':telephone', $sTelephone);
        $stmt->bindParam(':email', $sEmail);
        $stmt->bindParam(':gender', $sGenre);
        $stmt->bindParam(':statut', $sStatut);
		
		foreach($_enregistrements as $cellule)
		{
			var_dump($cellule);
			$aDataPerson = unserialize(urldecode($cellule));
			
			
			$sPrenom = $aDataPerson[0];
			$sNom = $aDataPerson[1];
			$sAdresse = $aDataPerson[2];
			$sTelephone = $aDataPerson[3];
            $sEmail = $aDataPerson[4];
            $sGenre = $aDataPerson[5];
            $sStatut = $aDataPerson[6];
            
			
			$stmt->execute();
			$iCpt++;
		}
		return 'Mise à jour réussie ! '.$iCpt.' enregistrement(s) modifié(s)';
	}
	catch(PDOException $e)
	{
		return 'Probleme UPDATE : '.$e->getMessage();
	}
}

/**Offres */
//#######################################################################################
// showOffre($_dbh)                                                                     #
// Affiche la liste des offres avec cases à cocher pour suppression                     #
//#######################################################################################
function showOffre($_dbh)
{
    $sTable  = '<form method="post" action="">';
    $sTable .= '<table class="prettyTable">';
    $sTable .= '<thead>';
    $sTable .= '    <tr>';
    $sTable .= '        <th><input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)"> Sélectionner tout</th>';
    $sTable .= '        <th>ID</th>';
    $sTable .= '        <th>Titre</th>';
    $sTable .= '        <th>Description</th>';
    $sTable .= '        <th>Destination</th>';
    $sTable .= '        <th>Logement</th>';
    $sTable .= '        <th>Type Transport</th>';
    $sTable .= '        <th>Durée (jours)</th>';
    $sTable .= '        <th>Prix</th>';
    $sTable .= '        <th>Date départ</th>';
    $sTable .= '        <th>Date retour</th>';
    $sTable .= '        <th>Disponibilité</th>';
    $sTable .= '    </tr>';
    $sTable .= '</thead>';
    $sTable .= '<tbody>';

    $sQuery = 'SELECT o.*, d.ville as destination_ville, l.nom as logement_nom 
               FROM offres o 
               JOIN destinations d ON o.destination_id = d.id 
               JOIN logements l ON o.logement_id = l.id 
               ORDER BY o.id';
    $stmt = $_dbh->prepare($sQuery);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sTable .= '<tr>';
        $sTable .= '<td><input type="checkbox" name="offres_a_supprimer[]" value="'.$row['id'].'"></td>';
        $sTable .= '<td>'.$row['id'].'</td>';
        $sTable .= '<td>'.$row['titre'].'</td>';
        $sTable .= '<td>'.substr($row['description'], 0, 50).'...</td>';
        $sTable .= '<td>'.$row['destination_ville'].'</td>';
        $sTable .= '<td>'.$row['logement_nom'].'</td>';
        $sTable .= '<td>'.$row['type_transport'].'</td>';
        $sTable .= '<td>'.$row['duree_sejour'].'</td>';
        $sTable .= '<td>'.$row['prix'].' €</td>';
        $sTable .= '<td>'.$row['date_depart'].'</td>';
        $sTable .= '<td>'.$row['date_retour'].'</td>';
        $sTable .= '<td>'.($row['disponibilite'] ? 'Disponible' : 'Complet').'</td>';
        $sTable .= '</tr>';
    }

    $sTable .= '</tbody>';
    $sTable .= '</table>';
    $sTable .= '<br><input type="submit" name="supprimer_selection" value="Supprimer les offres sélectionnées">';
    $sTable .= '</form>';

    // JavaScript pour le bouton "tout sélectionner"
    $sTable .= '
    <script>
        function toggleCheckboxes(source) {
            let checkboxes = document.querySelectorAll("input[name=\'offres_a_supprimer[]\']");
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = source.checked;
            });
        }
    </script>';

    return $sTable;
}

//#######################################################################################
// formUpdateOffre($_nomFichier, $_pageEnCours, $_dbh)                                  #
// Gère l'import des offres depuis un fichier CSV                                       #
//#######################################################################################
function formUpdateOffre($_nomFichier, $_pageEnCours, $_dbh)
{
    $handle = fopen($_nomFichier, 'rb');
    if($handle === false) {
        return 'Problème lors de l\'ouverture du fichier';
    }

    try {
        // Préparation des requêtes
        $sQueryInsert = 'INSERT INTO offres (titre, description, destination_id, logement_id, type_transport, 
                         duree_sejour, prix, date_depart, date_retour, disponibilite, images) 
                         VALUES (:titre, :description, :destination_id, :logement_id, :type_transport, 
                         :duree_sejour, :prix, :date_depart, :date_retour, :disponibilite, :images)';
        $stmtInsert = $_dbh->prepare($sQueryInsert);
        
        $sQueryCheck = 'SELECT id FROM offres WHERE titre = :titre AND date_depart = :date_depart';
        $stmtCheck = $_dbh->prepare($sQueryCheck);
        
        $iCptAdd = 0;
        $iCptDoublon = 0;
        
        $sForm = '<form method="POST" action="'.$_pageEnCours.'">';
        $sForm .= '<table class="prettyTable">';
        $sForm .= '<thead>';
        $sForm .= '    <tr>';
        $sForm .= '        <th>Titre';
        $sForm .= '        <th>Description';
        $sForm .= '        <th>Destination';
        $sForm .= '        <th>Logement';
        $sForm .= '        <th>Transport';
        $sForm .= '        <th>Durée';
        $sForm .= '        <th>Prix';
        $sForm .= '        <th>Dates';
        $sForm .= '        <th>Mettre à jour';
        $sForm .= '<tbody>';
        
        while($row = fgetcsv($handle, 1000, ':')) {
            // Structure du CSV attendu:
            // 0:titre, 1:description, 2:destination_id, 3:logement_id, 4:type_transport, 
            // 5:duree_sejour, 6:prix, 7:date_depart, 8:date_retour, 9:disponibilite, 10:images
            
            $aData = [
                'titre' => $row[0] ?? '',
                'description' => $row[1] ?? '',
                'destination_id' => $row[2] ?? 0,
                'logement_id' => $row[3] ?? 0,
                'type_transport' => $row[4] ?? 'avion',
                'duree_sejour' => $row[5] ?? 0,
                'prix' => $row[6] ?? 0,
                'date_depart' => $row[7] ?? date('Y-m-d'),
                'date_retour' => $row[8] ?? date('Y-m-d'),
                'disponibilite' => $row[9] ?? 0,
                'images' => $row[10] ?? ''
            ];
            
            // Vérifier si l'offre existe déjà
            $stmtCheck->execute(['titre' => $aData['titre'], 'date_depart' => $aData['date_depart']]);
            
            if($stmtCheck->rowCount() === 0) {
                // Insertion si l'offre n'existe pas
                $stmtInsert->execute($aData);
                $iCptAdd++;
            } else {
                // Formulaire pour les doublons
                $iCptDoublon++;
                
                // Récupérer les infos de la destination et du logement
                $sQueryDest = 'SELECT ville FROM destinations WHERE id = :id';
                $stmtDest = $_dbh->prepare($sQueryDest);
                $stmtDest->execute(['id' => $aData['destination_id']]);
                $dest = $stmtDest->fetch(PDO::FETCH_ASSOC);
                
                $sQueryLog = 'SELECT nom FROM logements WHERE id = :id';
                $stmtLog = $_dbh->prepare($sQueryLog);
                $stmtLog->execute(['id' => $aData['logement_id']]);
                $log = $stmtLog->fetch(PDO::FETCH_ASSOC);
                
                // Nouvelle offre (CSV)
                $sForm .= '    <tr>';
                $sForm .= '        <td>'.$aData['titre'];
                $sForm .= '        <td>'.substr($aData['description'], 0, 30).'...';
                $sForm .= '        <td>'.($dest['ville'] ?? 'Inconnue');
                $sForm .= '        <td>'.($log['nom'] ?? 'Inconnu');
                $sForm .= '        <td>'.$aData['type_transport'];
                $sForm .= '        <td>'.$aData['duree_sejour'];
                $sForm .= '        <td>'.$aData['prix'].' €';
                $sForm .= '        <td>'.$aData['date_depart'].' au '.$aData['date_retour'];
                $sForm .= '        <td>new <input type="checkbox" name="data[]" value="'.urlencode(serialize($aData)).'">';
                
                // Offre existante (DB)
                $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                $sQueryExist = 'SELECT o.*, d.ville as destination_ville, l.nom as logement_nom 
                               FROM offres o 
                               JOIN destinations d ON o.destination_id = d.id 
                               JOIN logements l ON o.logement_id = l.id 
                               WHERE o.id = :id';
                $stmtExist = $_dbh->prepare($sQueryExist);
                $stmtExist->execute(['id' => $existing['id']]);
                $existData = $stmtExist->fetch(PDO::FETCH_ASSOC);
                
                $sForm .= '    <tr class="oldData">';
                $sForm .= '        <td>'.$existData['titre'];
                $sForm .= '        <td>'.substr($existData['description'], 0, 30).'...';
                $sForm .= '        <td>'.$existData['destination_ville'];
                $sForm .= '        <td>'.$existData['logement_nom'];
                $sForm .= '        <td>'.$existData['type_transport'];
                $sForm .= '        <td>'.$existData['duree_sejour'];
                $sForm .= '        <td>'.$existData['prix'].' €';
                $sForm .= '        <td>'.$existData['date_depart'].' au '.$existData['date_retour'];
                $sForm .= '        <td>old';
            }
        }
        
        fclose($handle);
        
        $sForm .= '</table>';
        $sForm .= '<input type="hidden" name="actionPOST" value="importOffre">';
        $sForm .= '<input type="submit" value="Mettre à jour">';
        $sForm .= '</form>';
        
        return $iCptAdd.' offre(s) ajoutée(s)<br><b>Il y a '.$iCptDoublon.' offre(s) déjà présente(s)</b>'.$sForm;
        
    } catch(PDOException $e) {
        return 'Erreur lors de l\'insertion dans la DB : '.$e->getMessage();
    }
}

//#######################################################################################
// updateOffre($_enregistrements, $_dbh)                                                #
// Met à jour les offres sélectionnées                                                  #
//#######################################################################################
function updateOffre($_enregistrements, $_dbh)
{
    try {
        $iCpt = 0;
        $sQuery = 'UPDATE offres SET 
                   titre = :titre,
                   description = :description,
                   destination_id = :destination_id,
                   logement_id = :logement_id,
                   type_transport = :type_transport,
                   duree_sejour = :duree_sejour,
                   prix = :prix,
                   date_depart = :date_depart,
                   date_retour = :date_retour,
                   disponibilite = :disponibilite,
                   images = :images
                   WHERE titre = :titre AND date_depart = :date_depart';
        
        $stmt = $_dbh->prepare($sQuery);
        
        foreach($_enregistrements as $cellule) {
            $aData = unserialize(urldecode($cellule));
            
            // Valeurs par défaut
            $defaultData = [
                'titre' => '',
                'description' => '',
                'destination_id' => 0,
                'logement_id' => 0,
                'type_transport' => 'avion',
                'duree_sejour' => 0,
                'prix' => 0,
                'date_depart' => date('Y-m-d'),
                'date_retour' => date('Y-m-d'),
                'disponibilite' => 0,
                'images' => ''
            ];
            
            // Fusion avec les valeurs par défaut
            $aData = array_merge($defaultData, $aData);
            
            $stmt->execute([
                'titre' => $aData['titre'],
                'description' => $aData['description'],
                'destination_id' => $aData['destination_id'],
                'logement_id' => $aData['logement_id'],
                'type_transport' => $aData['type_transport'],
                'duree_sejour' => $aData['duree_sejour'],
                'prix' => $aData['prix'],
                'date_depart' => $aData['date_depart'],
                'date_retour' => $aData['date_retour'],
                'disponibilite' => $aData['disponibilite'],
                'images' => $aData['images']
            ]);
            
            $iCpt++;
        }
        
        return 'Mise à jour réussie ! '.$iCpt.' offre(s) modifiée(s)';
        
    } catch(PDOException $e) {
        return 'Problème lors de la mise à jour : '.$e->getMessage();
    }
}
//#######################################################################################
// formCreateOffre($_dbh, $_pageEnCours)                                               #
// Affiche le formulaire de création d'une nouvelle offre                              #
//#######################################################################################
function formCreateOffre($_dbh, $_pageEnCours)
{
    // Récupérer les destinations et logements pour les menus déroulants
    $destinations = $_dbh->query("SELECT id, ville FROM destinations")->fetchAll(PDO::FETCH_ASSOC);
    $logements = $_dbh->query("SELECT id, nom FROM logements")->fetchAll(PDO::FETCH_ASSOC);
    
    $optionsDest = '';
    foreach ($destinations as $dest) {
        $optionsDest .= "<option value='{$dest['id']}'>{$dest['ville']}</option>";
    }
    
    $optionsLog = '';
    foreach ($logements as $log) {
        $optionsLog .= "<option value='{$log['id']}'>{$log['nom']}</option>";
    }
    
    $form = <<<EOT
    <form method="POST" action="{$_pageEnCours}" enctype="multipart/form-data">
        <input type="hidden" name="actionPOST" value="createOffre">
        
        <div class="mb-3">
            <label class="form-label">Titre de l'offre</label>
            <input type="text" class="form-control" name="titre" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Destination</label>
                <select class="form-select" name="destination_id" required>
                    <option value="">Choisir une destination</option>
                    {$optionsDest}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Logement</label>
                <select class="form-select" name="logement_id" required>
                    <option value="">Choisir un logement</option>
                    {$optionsLog}
                </select>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Type de transport</label>
                <select class="form-select" name="type_transport" required>
                    <option value="avion">Avion</option>
                    <option value="bus">Bus</option>
                    <option value="train">Train</option>
                    <option value="covoiturage">Covoiturage</option>
                    <option value="voiture">Voiture</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Durée (jours)</label>
                <input type="number" class="form-control" name="duree_sejour" min="1" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Prix (€)</label>
                <input type="number" step="0.01" class="form-control" name="prix" min="0" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Date de départ</label>
                <input type="date" class="form-control" name="date_depart" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date de retour</label>
                <input type="date" class="form-control" name="date_retour" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Disponibilité</label>
            <select class="form-select" name="disponibilite" required>
                <option value="1">Disponible</option>
                <option value="0">Complet</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Image (optionnel)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>
        
        <button type="submit" class="btn btn-primary">Créer l'offre</button>
    </form>
EOT;

    return $form;
}

//#######################################################################################
// createOffre($_data, $_dbh)                                                          #
// Crée une nouvelle offre dans la base de données                                      #
//#######################################################################################
function createOffre($_data, $_files, $_dbh)
{
    try {
        // Gestion de l'upload de l'image
        $imageName = null;
        if (!empty($_files['image']['name'])) {
            $uploadDir = 'uploads/offres/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $imageName = uniqid() . '_' . basename($_files['image']['name']);
            $targetFile = $uploadDir . $imageName;
            
            if (!move_uploaded_file($_files['image']['tmp_name'], $targetFile)) {
                return "<div class='alert alert-danger'>Erreur lors de l'upload de l'image.</div>";
            }
        }
        
        // Préparation de la requête
        $sQuery = 'INSERT INTO offres (titre, description, destination_id, logement_id, type_transport, 
                   duree_sejour, prix, date_depart, date_retour, disponibilite, images) 
                   VALUES (:titre, :description, :destination_id, :logement_id, :type_transport, 
                   :duree_sejour, :prix, :date_depart, :date_retour, :disponibilite, :images)';
        
        $stmt = $_dbh->prepare($sQuery);
        
        // Exécution de la requête
        $stmt->execute([
            'titre' => $_data['titre'],
            'description' => $_data['description'],
            'destination_id' => $_data['destination_id'],
            'logement_id' => $_data['logement_id'],
            'type_transport' => $_data['type_transport'],
            'duree_sejour' => $_data['duree_sejour'],
            'prix' => $_data['prix'],
            'date_depart' => $_data['date_depart'],
            'date_retour' => $_data['date_retour'],
            'disponibilite' => $_data['disponibilite'],
            'images' => $imageName
        ]);
        
        return "<div class='alert alert-success'>L'offre a été créée avec succès!</div>";
        
    } catch(PDOException $e) {
        return "<div class='alert alert-danger'>Erreur lors de la création de l'offre: " . $e->getMessage() . "</div>";
    }
}