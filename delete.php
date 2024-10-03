<?php
$databaseFile = 'data/database.db'; // Chemin vers le fichier de base de données

// Établir une connexion à la base de données SQLite
$db = new SQLite3($databaseFile);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Assure-toi que c'est un entier
    // Supprime le lien de la table link
    $db->exec("DELETE FROM link WHERE id = $id");
    // Supprime également les statistiques associées
    $db->exec("DELETE FROM stat WHERE source = $id");
}

$db->close();

header('Location: link.php');
exit;
?>
