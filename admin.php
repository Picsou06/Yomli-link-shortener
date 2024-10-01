<?php
/**
 * Go admin page.
 *
 * If you're wondering what this is all about,
 * check out the README.md file.
 *
 * Project repo:        https://github.com/yomli/yomli-go
 *
 */

include 'data/config.php';
include 'core/lib.php';
$databaseFile = 'data/base.php';

// Password protection
if (isset($privateGo)) {
    $auto_restrict['root'] = './';
    $auto_restrict['path_from_root'] = 'core/auto_restrict';
    $auto_restrict['redirect_success'] = 'admin.php';
    include $auto_restrict['root'] . $auto_restrict['path_from_root'] . '/auto_restrict.php';
}

// Load base
if (is_file($databaseFile)) {
    $base = load($databaseFile);
} else {
    $base = [];
}

// Import base
if (isset($_FILES['import'])) {
    $file = 'data/go-import.json';
    $success = uploadFile($_FILES['import'], $file);
    if (!is_array($success)) {
        $json = readJSON($file);
        if (is_array($json)) {
            foreach ($json as $code => $url) {
                $base[$code] = [
                    'url' => strip_tags($url),
                    'nb' => 0 // Initialiser le compteur de visites à 0
                ];
            }
            save($databaseFile, $base);
            $msg = "Les liens ont bien été importés.";
        } else {
            $msg = "Erreur de lecture du fichier.";
        }
    } else {
        $msg = "Il y a eu un problème avec l'upload du fichier&nbsp;:";
        foreach ($success as $key => $value) {
            $msg .= "<br />" . $value;
        }
    }
}

// Export base
if (isset($_GET['export'])) {
    if (!empty($base)) {
        $export = 'data/go-export_' . date('Y-m-d_His') . '.json';
        $success = writeJSON($base, $export);
        if ($success) {
            $success = downloadFile($export);
            if (!$success) {
                $msg = "Problème de téléchargement.";
                $msg .= "Votre export est disponible à l'emplacement <a href='" . $export . "'>" . $export . "</a>";
            }
        } else {
            $msg = "Erreur d'écriture.";
        }
    } else {
        $msg = "Il n'y a rien à exporter.";
    }
}

// Delete URL
if (isset($_GET['delete'])) {
    if (!empty($base[$_GET['delete']])) {
        unset($base[$_GET['delete']]);
        save($databaseFile, $base);
        $msg = 'Redirection supprimée.';
    }
}

// Purge base
if (isset($_GET['purge'])) {
    $base = [];
    save($databaseFile, $base);
    $msg = 'Base purgée.';
}
?>

<?php include 'tpl/header.html'; ?>

<a href="<?php echo (isset($privateGo)) ? '?logout=ok' : 'index.php'; ?>" class="button" id="button-admin" title="Déconnexion">⚙️</a>

<?php if (empty($msg)) { ?>
    <h2>Importer des liens</h2>
    <form action="admin.php" method="post" enctype="multipart/form-data">
        <input type="file" name="import" />
        <input type="submit" name="submit" value="Importer des URL" />
        <?php if (isset($privateGo)) { newToken(); } ?>
        <a href="admin.php?export&token=<?php if (isset($privateGo)) { newToken(true); } ?>" class="button text">Exporter les URL</a>
    </form>

    <h2>Supprimer un lien</h2>
    <?php if (!empty($base)) { ?>

        <form action="admin.php" method="get">
            <input required="true" type="text" name="delete" placeholder="Tapez le texte du raccourci" />
            <input type="submit" value="Supprimer" />
            <?php if (isset($privateGo)) { newToken(); } ?>
        </form>

        <table>
            <tr>
                <th>URL</th>
                <th>Code</th>
                <th>Nombre de visites</th> <!-- Ajout de l'en-tête pour le compteur -->
                <?php if (!empty($qrcodeAPI)) { echo '<th>&nbsp;</th>'; } ?>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($base as $code => $data) { // Utilisation de $data pour accéder aux détails ?>
                <tr>
                    <td><a href="<?php echo $data['url']; ?>"><?php echo $data['url']; ?></a></td>
                    <td><input type="text" value="<?php echo $code; ?>" onclick="copyThis('<?php echo addSlash(str_replace('admin.php','',getURL())).'?'.$code; ?>', this); select()" /></td>
                    <td><?php echo $data['nb']; // Affichage du nombre de visites ?></td> <!-- Affichage du compteur -->
                    <?php if (!empty($qrcodeAPI)) { echo '<td><a href="'.$qrcodeAPI.urlencode($data['url']).'" target="_blank" title="QRCode">📱</a></td>'; } ?>
                    <td><a href="admin.php?delete=<?php echo $code; ?>&token=<?php if (isset($privateGo)) { newToken(true); } ?>" class="button" title="Supprimer">❌</a></td>
                </tr>
            <?php } ?>
        </table>

        <h2>Purger les liens</h2>
        <mark>Attention, cela va supprimer tous les liens !
            <a href="admin.php?purge&token=<?php if (isset($privateGo)) { newToken(true); } ?>" class="button text" id="button-purge">☠️&nbsp;Purger</a>
        </mark>

        <script type="text/javascript" src="tpl/script.js"></script>

    <?php } else { echo '<h3>Aucun lien à supprimer.</h3>'; } ?>

    <h2>Bookmarklet</h2>
    <p>Placez ce lien dans vos favoris pour raccourcir rapidement une URL : <a href="javascript:void(location.href='<?php echo addSlash(str_replace('admin.php','',getURL())) ?>index.php?code=&url='+encodeURIComponent(document.location.href));">▶️⏩ Raccourcir l'URL</a></p>

<?php } else { echo '<h3>'.$msg.'</h3>'; ?>
    <a href="admin.php" class="button" id="button-back" title="Retour">⬅️</a>
<?php } ?>

<?php include 'tpl/footer.html'; ?>
