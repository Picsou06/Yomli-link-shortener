<?php
/**
 * Go index page.
 *
 * If you're wondering what this is all about,
 * check out the README.md file.
 *
 * Project repo: https://github.com/yomli/yomli-go
 *
 */

include 'data/config.php';
include 'core/lib.php';

// Base de données SQLite
$databaseFile = 'data/database.db';
$db = new SQLite3($databaseFile);

// Créer les tables si elles n'existent pas
$db->exec("CREATE TABLE IF NOT EXISTS link (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    source TEXT,
    destination TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS stat (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    source INTEGER,
    ip TEXT,
    location TEXT,
    browser TEXT,
    os TEXT,
    time TEXT,
    day TEXT,
    FOREIGN KEY (source) REFERENCES link(id)
)");

// Password protection
if ($privateGo) {
    if (isset($_GET['url']) || empty($_GET)) {
        $auto_restrict['root'] = './';
        $auto_restrict['path_from_root'] = 'core/auto_restrict';
        $auto_restrict['redirect_success'] = 'index.php';
        include $auto_restrict['root'] . $auto_restrict['path_from_root'] . '/auto_restrict.php';
    }
}

// Ajouter une nouvelle URL
if (isset($_GET['url'])) {
    // Générer un code unique si nécessaire
    if (empty($_GET['code'])) {
        $code = uniqid(); // Générer un code unique
    } else {
        $code = preg_replace('#[^a-zA-Z0-9]#', '', $_GET['code']);
    }

    // Vérifier si la source existe déjà
    $stmtCheck = $db->prepare("SELECT id FROM link WHERE source = :code");
    $stmtCheck->bindValue(':code', $code, SQLITE3_TEXT);
    $resultCheck = $stmtCheck->execute();
    
    if ($row = $resultCheck->fetchArray(SQLITE3_ASSOC)) {
        // La source existe déjà, générer un nouveau uniqid de 4 à 6 caractères
        $code = substr(uniqid(), -4); // Prendre les 4 derniers caractères du uniqid
    }

    // Insérer l'URL dans la table link
    $stmt = $db->prepare("INSERT INTO link (source, destination) VALUES (:code, :url)");
    $stmt->bindValue(':code', $code, SQLITE3_TEXT);
    $stmt->bindValue(':url', strip_tags($_GET['url']), SQLITE3_TEXT);
    $stmt->execute();

    $question = ($stripQuestion) ? '' : '?';
    $str = addSlash(str_replace('index.php', '', getURL())) . $question . $code;
    $msg = 'Votre URL raccourcie&nbsp;: ';
    $msg .= '<a href="' . $str . '" data-code="' . $code . '" id="short-link">' . $str . '</a></strong>';
}


// Autres paramètres
if (!empty($_GET) && count($_GET) == 1) {
    $get = array_keys($_GET);
    $get = $get[0];

    // Vérifier si c'est un lien raccourci
    $stmt = $db->prepare("SELECT * FROM link WHERE source = :source");
    $stmt->bindValue(':source', $get, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Incrémenter le compteur de visites dans la table stat
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = getBrowserInfo(); // Utiliser la fonction pour obtenir le navigateur
        $os = getOS(); // Utiliser la fonction pour obtenir le système d'exploitation

        // Obtenir la localisation
        $location = 'Inconnu'; // Définir la valeur par défaut
        $locationData = json_decode(file_get_contents("https://ipapi.co/{$ip}/json/"), true);
        if (!empty($locationData['city']) && !empty($locationData['region']) && !empty($locationData['country_name'])) {
            $location = "{$locationData['city']}, {$locationData['region']}, {$locationData['country_name']}";
        }

        // Obtenir l'heure et le jour actuels
        $currentTime = date('H:i:s'); // Format 24 heures
        $currentDay = date('Y-m-d'); // Format AAAA-MM-JJ

        // Insérer les statistiques dans la table stat
        $stmt = $db->prepare("INSERT INTO stat (source, ip, location, browser, os, time, day) VALUES ((SELECT id FROM link WHERE source = :source), :ip, :location, :browser, :os, :time, :day)");
        $stmt->bindValue(':source', $get, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':location', $location, SQLITE3_TEXT);
        $stmt->bindValue(':browser', $browser, SQLITE3_TEXT);
        $stmt->bindValue(':os', $os, SQLITE3_TEXT);
        $stmt->bindValue(':time', $currentTime, SQLITE3_TEXT);
        $stmt->bindValue(':day', $currentDay, SQLITE3_TEXT);
        $stmt->execute();

        // Rediriger vers le lien original
        header("Location: " . $row['destination']);
        exit;
    } else {
        $msg = 'URL inconnue.';
    }
}

// Function to get browser information
function getBrowserInfo() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($userAgent, 'Firefox') !== false) {
        return 'Mozilla Firefox';
    } elseif (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Chromium') === false && strpos($userAgent, 'Edg') === false) {
        return 'Google Chrome';
    } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
        return 'Apple Safari';
    } elseif (strpos($userAgent, 'Edg') !== false) {
        return 'Microsoft Edge';
    } elseif (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
        return 'Opera';
    } elseif (strpos($userAgent, 'Trident') !== false || strpos($userAgent, 'MSIE') !== false) {
        return 'Internet Explorer';
    }

    return 'Unknown';
}


// Function to get operating system information
function getOS() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (stripos($userAgent, 'Win') !== false) {
        return 'Windows';
    } elseif (stripos($userAgent, 'Mac') !== false) {
        return 'macOS';
    } elseif (stripos($userAgent, 'Linux') !== false) {
        return 'Linux';
    } elseif (stripos($userAgent, 'Android') !== false) {
        return 'Android';
    } elseif (stripos($userAgent, 'iOS') !== false || stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
        return 'iOS';
    }

    return 'Unknown';
}


?>

<?php include 'tpl/header.html'; ?>

<a href="link.php" class="button" id="button-user">Liens</a>

<?php if (empty($msg)) { ?>

    <form action="index.php" method="get">
        <input type="url" name="url" placeholder="URL à raccourcir" required />
        <input type="text" name="code" placeholder="Raccourci personnalisé (optionnel)"/>
        <input type="submit" value="Go&nbsp;!"/>
        <?php if ($privateGo) { newToken(); } ?>
    </form>

<?php } else {
    echo '<h3>' . $msg . '</h3><a href="' . $_SERVER['HTTP_REFERER'] . '" class="button" id="button-back" title="Retour">⬅️</a>';
    if (!empty($qrcodeAPI) && isset($_GET['url'])) {
        echo '<img src="' . $qrcodeAPI . urlencode($_GET["url"]) . '" class="qrcode" />';
    } ?>
    <script type="text/javascript" src="tpl/script.min.js"></script>
    <script type="text/javascript">//<![CDATA[
        var copyButton = document.getElementById('clipboard');
        copyButton.classList.remove('hidden');
    //]]></script>
<?php } ?>


<?php if (isset($_GET['url'])) { ?>
    <script type="text/javascript">//<![CDATA[
        ;(function (window, document, undefined) {
            // Feature test for localStorage
            if (!('localStorage' in window)) return;
            var shortLink = document.getElementById('short-link').getAttribute('data-code');
            var data = [];
            // Get links
            var localLinks = localStorage.getItem('go--links');
            if (!localLinks) {
                data[0] = shortLink;
            } else {
                data = JSON.parse(localLinks);
                data.push(shortLink);
            }
            localStorage.setItem('go--links', JSON.stringify(data));
        })(window, document);
    //]]></script>
<?php } ?>

<script type="text/javascript">//<![CDATA[
    ;(function (window, document, undefined) {
        // Feature test for localStorage
        if (!('localStorage' in window)) return;

        // Get links
        var localLinks = localStorage.getItem('go--links');
        if (!localLinks) return;

        // Create a form to POST
        var form = document.createElement('form');
        form.style.visibility = 'hidden';
        form.method = 'POST';
        form.action = 'link.php';
        var input = document.createElement('input');
        input.name = 'links';
        input.value = localLinks;
        form.appendChild(input);
        document.body.appendChild(form);

        // Unhide 'My links'
        var userButton = document.getElementById('button-user');
        userButton.classList.remove('hidden');

        // On click, post to user
        userButton.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            form.submit();
        }, false);
    })(window, document);
    //]]></script>

<?php include 'tpl/footer.html'; ?>
