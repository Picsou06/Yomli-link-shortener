<?php
/**
 * Go My Links page.
 *
 * If you're wondering what this is all about,
 * check out the README.md file.
 *
 * Project repo:        https://github.com/yomli/yomli-go
 *
 */

include 'data/config.php';
include 'core/lib.php';

$databaseFile = 'data/database.db'; // Chemin vers le fichier de base de données

// Établir une connexion à la base de données SQLite
$db = new SQLite3($databaseFile);

// Password protection
if ($privateGo) {
    if (isset($_GET['url']) || empty($_GET)) {
        $auto_restrict['root'] = './';
        $auto_restrict['path_from_root'] = 'core/auto_restrict';
        $auto_restrict['redirect_success'] = 'link.php';
        include $auto_restrict['root'] . $auto_restrict['path_from_root'] . '/auto_restrict.php';
    }
}

$links = [];
$result = $db->query("SELECT * FROM link");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    // Pour chaque lien, on récupère le nombre de visites dans la même requête
    $statResult = $db->query("SELECT COUNT(*) as visitCount FROM stat WHERE source = " . $row['id']);
    $statRow = $statResult->fetchArray(SQLITE3_ASSOC);
    $row['visitCount'] = $statRow['visitCount'] ?? 0; // Ajoute le nombre de visites au tableau du lien
    $links[] = $row; // Ajoute le lien avec le nombre de visites
}

// Fermer la connexion à la base de données
$db->close();
?>
<?php include 'tpl/header.html'; ?>
<style>
    /* Ajout des styles CSS pour le tableau */
    table {
        border-collapse: collapse; /* Pour éviter les doubles bordures */
        width: 100%; /* Optionnel : pour rendre le tableau responsive */
    }
    
    th, td {
        border: 1px solid #ddd; /* Bordure pour chaque cellule */
        padding: 8px; /* Espacement à l'intérieur des cellules */
        text-align: center; /* Centre le contenu des cellules */
    }
    
    .delete-link {
        color: red; /* Couleur de la croix */
        cursor: pointer; /* Changer le curseur pour indiquer que c'est cliquable */
        font-weight: bold; /* Mettre en gras */
    }
</style>

<a href="index.php" class="button" id="button-admin" title="Retour">🏠</a>

<?php if (empty($msg)) { ?>
    <h2>Liens</h2>
    <?php if (!empty($links)) { ?>
        <table>
            <tr>
                <th>URL</th>
                <th>Source</th>
                <th>Nombre de visites</th>
                <th>Supprimer</th> <!-- Nouvelle colonne pour la suppression -->
            </tr>
            <?php foreach ($links as $link) { ?>
                <tr>
                    <td><a href="<?php echo htmlspecialchars($link['destination']); ?>"><?php echo htmlspecialchars($link['destination']); ?></a></td>
                    <td><a href="https://link.Picsou06.fun/<?php echo htmlspecialchars($link['source']); ?>"><?php echo htmlspecialchars($link['source']); ?></a></td>
                    <td>
                        <a href="stat.php?id=<?php echo $link['id']; ?>"><?php echo $link['visitCount']; ?></a> <!-- Lien vers la page de statistiques -->
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="delete-link" onclick="confirmDelete(<?php echo $link['id']; ?>)">❌</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>Aucun lien disponible.</p>
    <?php } ?>
    <script type="text/javascript" src="tpl/script.js"></script>

    <script>
        function confirmDelete(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce lien ?")) {
                window.location.href = 'delete.php?id=' + id; // Redirige vers delete.php avec l'ID
            }
        }
    </script>

<?php } else { echo '<h3>' . htmlspecialchars($msg) . '</h3>'; ?>
    <a href="link.php" class="button" id="button-back" title="Accueil">⬅️</a>
<?php } ?>

<?php include 'tpl/footer.html'; ?>
