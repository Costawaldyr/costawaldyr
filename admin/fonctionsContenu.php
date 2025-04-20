<?php


/**
 * Récupère toutes les sections actives
 */
function getSectionsActives() {
    global $dbh;
    return $dbh->query("SELECT * FROM sections WHERE est_actif = TRUE ORDER BY ordre")->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les contenus actifs d'une section
 */
function getContenusBySection($section_slug) {
    global $dbh;
    $stmt = $dbh->prepare("
        SELECT * FROM contenus 
        WHERE position = ? AND est_actif = TRUE 
        AND (date_debut IS NULL OR date_debut <= NOW())
        AND (date_fin IS NULL OR date_fin >= NOW())
        ORDER BY ordre
    ");
    $stmt->execute([$section_slug]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Génère le HTML d'une section avec son contenu
 */
function afficherSection($section) {
    $contenus = getContenusBySection($section['slug']);
    
    // Vérifier si la section doit être affichée
    if (empty($contenus)) {
        if (!$section['toujours_afficher']) {
            return '';
        }
        // Si la section doit toujours être affichée mais n'a pas de contenu
        $contenus = [['type' => 'texte', 'contenu' => '']];
    }
    
    $html = '<section id="' . htmlspecialchars($section['slug'] ?? '') . '" class="' . htmlspecialchars($section['classes_css'] ?? '') . '">';
    
    if (!empty($section['titre'])) {
        $html .= '<h2 class="section-title">' . htmlspecialchars($section['titre']) . '</h2>';
    }
    
    foreach ($contenus as $contenu) {
        $html .= genererContenuHTML($contenu);
        
    }

    
    
    $html .= '</section>';
    
    return $html;
}

/**
 * Génère le HTML d'un contenu selon son type
 */function genererContenuHTML($contenu) {
    // Vérifier et initialiser les valeurs qui pourraient être null
    $type = $contenu['type'] ?? '';
    $style = $contenu['style'] ?? '';
    $url = $contenu['url'] ?? '';
    $titre = $contenu['titre'] ?? '';
    $image_url = $contenu['image_url'] ?? '';
    
    $html = '<div class="contenu-' . htmlspecialchars($type) . '" style="' . htmlspecialchars($style) . '">';
    
    switch ($type) {
        case 'texte':
            $html .= $contenu['contenu'] ?? ''; // Contenu HTML déjà formaté
            break;
            
        case 'image':
            $html .= '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($titre) . '">';
            if (!empty($contenu['contenu'])) {
                $html .= '<div class="legende">' . ($contenu['contenu'] ?? '') . '</div>';
            }
            break;
            
        case 'video':
            $html .= '<video controls><source src="' . htmlspecialchars($url) . '"></video>';
            if (!empty($contenu['contenu'])) {
                $html .= '<div class="description">' . ($contenu['contenu'] ?? '') . '</div>';
            }
            break;
            
        case 'publicite':
            $html .= '<div class="pub"><a href="' . htmlspecialchars($url) . '" target="_blank">';
            $html .= '<img src="' . htmlspecialchars($image_url) . '" alt="' . htmlspecialchars($titre) . '">';
            $html .= '</a></div>';
            break;
        
            case 'promotion':
                $html .= '<div class="promotion-banner" style="background-color: ' . htmlspecialchars($contenu['couleur_fond'] ?? '#FF5E5B') . '">';
                $html .= '<div class="promotion-content">';
                $html .= '<h3>' . htmlspecialchars($titre) . '</h3>';
                $html .= '<div>' . ($contenu['contenu'] ?? '') . '</div>';
                $html .= '</div></div>';
                break;
            
            case 'carousel':
                $html .= '<div class="carousel-container">';
                // Logique pour un carousel d'images
                $html .= '</div>';
                break;
            
            case 'bouton':
                $html .= '<a href="' . htmlspecialchars($url) . '" class="custom-button" style="' . htmlspecialchars($style) . '">';
                $html .= htmlspecialchars($titre);
                $html .= '</a>';
                break;
    }
    
    $html .= cleanHTML($contenu['contenu'] ?? '');
    $html .= '</div>';
    
    return $html;
}


function cleanHTML($html) {
    // Autoriser seulement certaines balises HTML
    $allowed_tags = '<p><a><strong><em><span><div><img><video><h1><h2><h3><h4><h5><h6><ul><ol><li><table><tr><td><th>';
    return strip_tags($html, $allowed_tags);
}