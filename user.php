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
$databaseFile = 'data/base.php';

// Password protection
if ($privateGo) {
    if (isset($_GET['url']) || empty($_GET)) {
        $auto_restrict['root'] = './';
        $auto_restrict['path_from_root'] = 'core/auto_restrict';
        $auto_restrict['redirect_success'] = 'user.php';
        include $auto_restrict['root'] . $auto_restrict['path_from_root'] . '/auto_restrict.php';
    }
}

// Load base
if (is_file($databaseFile)) {
    $base = load($databaseFile);
} else {
    $base = [];
}

$localBase = [];

if (isset($_POST['links'])) {
    $myLinks = json_decode($_POST['links'], true);
    
    foreach ($myLinks as $index => $code) {
        if (!empty($base[$code])) {
            $localBase[$code] = $base[$code];
        }
    }
}
?>

<?php include 'tpl/header.html'; ?>

<a href="index.php" class="button" id="button-admin" title="Retour">🏠</a>

<?php if (empty($msg)) { ?>
    <h2>Importer des liens</h2>
    <form action="" method="post">
        <input type="file" name="import" id="import-file" required />
        <input type="submit" name="submit" id="import-submit" value="Importer des URL"/>
        <?php
            $dataUri = 'data:application/json;charset=utf-8,' . urlencode(json_encode($localBase));
            $export = 'go-mylinks_' . date('Y-m-d_His') . '.json';
            echo '<a href="'.$dataUri.'" download="'.$export.'" class="button text" id="button-export">Exporter les URL</a>';
        ?>
    </form>

    <?php if (!empty($localBase)) { ?>
        <table>
            <tr>
                <th>URL</th>
                <th>Code</th>
                <th>Nombre de visites</th> <!-- Nouvelle colonne pour les visites -->
                <?php if (!empty($qrcodeAPI)) { echo '<th>&nbsp;</th>'; } ?>
            </tr>
            <?php foreach ($localBase as $code => $url) { 
                $question = ($stripQuestion) ? '' : '?';
                $str = addSlash(str_replace('user.php', '', getURL())) . $question . $code;
            ?>
                <tr>
                    <td><a href="<?php echo $url['url']; ?>"><?php echo $url['url']; ?></a></td>
                    <td><input type="text" value="<?php echo $code; ?>" onclick="copyThis('<?php echo $str; ?>', this); select()" /><a></a></td>
                    <td><?php echo isset($url['nb']) ? $url['nb'] : 0; ?></td> <!-- Affichage du nombre de visites -->
                    <?php if (!empty($qrcodeAPI)) { echo '<td><a href="'.$qrcodeAPI.urlencode($url['url']).'" target="_blank" title="QRCode">📱</a></td>'; } ?>
                </tr>
            <?php } ?>
        </table>

        <h2>Bookmarklet</h2>
        <p>Placez ce lien dans vos favoris pour raccourcir rapidement une URL : <a href="javascript:void(location.href='<?php echo addSlash(str_replace('admin.php', '', getURL())) ?>index.php?code=&url=' + encodeURIComponent(document.location.href));">▶️⏩ Raccourcir l'URL</a></p>

        <script type="text/javascript" src="tpl/script.js"></script>
    <?php } ?>

<?php } else { echo '<h3>' . $msg . '</h3>'; ?>
    <a href="user.php" class="button" id="button-back" title="Accueil">⬅️</a>
<?php } ?>

<script type="text/javascript">//<![CDATA[
    (function (window, document, undefined) {
        // Feature test for localStorage
        if (!('localStorage' in window)) return;
        // Check for the various File API support
        if (!(window.File && window.FileReader && window.FileList && window.Blob)) return;

        var fileInput = document.getElementById('import-file');
        fileInput.addEventListener('change', function (e) {
            // Read file
            reader = new FileReader();
            reader.readAsText(e.target.files[0]);
            reader.onload = function (readerEvent) {
                var content = readerEvent.target.result;
                var imported = JSON.parse(content);

                // Update localStorage
                var importButton = document.getElementById('import-submit');
                importButton.addEventListener('click', function (submitEvent) {
                    submitEvent.preventDefault();

                    var localLinks = localStorage.getItem('go--links');
                    var data = [];

                    if (localLinks) {
                        data = JSON.parse(localLinks);
                    }

                    for (var key in imported) {
                        data.push(key);
                    }

                    localStorage.setItem('go--links', JSON.stringify(data));

                    // Refresh using POST
                    var form = document.createElement('form');
                    form.style.visibility = 'hidden';
                    form.method = 'POST';
                    form.action = 'user.php';
                    var input = document.createElement('input');
                    input.name = 'links';
                    input.value = JSON.stringify(data);
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();

                }, false);
            };

        }, false);
    })(window, document);	
//]]></script>

<?php include 'tpl/footer.html'; ?>
