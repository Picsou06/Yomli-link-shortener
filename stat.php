<?php
/**
 * Page de statistiques des clics sur les liens.
 */

include 'data/config.php';
include 'core/lib.php';

$databaseFile = 'data/database.db'; // Chemin vers le fichier de base de données

// Établir une connexion à la base de données SQLite
$db = new SQLite3($databaseFile);

// Vérifie si l'ID du lien est fourni
$linkId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations du lien
$linkResult = $db->query("SELECT * FROM link WHERE id = $linkId");
$link = $linkResult->fetchArray(SQLITE3_ASSOC);

// Si le lien n'existe pas
if (!$link) {
    die("Lien introuvable.");
}

// Récupérer les statistiques de clics pour ce lien
$stats = [];
$statResult = $db->query("SELECT * FROM stat WHERE source = $linkId");
while ($row = $statResult->fetchArray(SQLITE3_ASSOC)) {
    $stats[] = $row;
}

// Récupérer les statistiques de visites pour les 7 derniers jours
$visitCounts = [];
$visitsResult = $db->query("
    SELECT DATE(day) as visit_date, COUNT(*) as visit_count 
    FROM stat 
    WHERE source = $linkId 
    AND day >= DATE('now', '-7 days') 
    GROUP BY visit_date
    ORDER BY visit_date ASC
");

while ($row = $visitsResult->fetchArray(SQLITE3_ASSOC)) {
    $visitCounts[$row['visit_date']] = $row['visit_count'];
}

// Si aucun résultat, remplissez les jours manquants avec des visites à 0
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    if (!isset($visitCounts[$date])) {
        $visitCounts[$date] = 0;
    }
}

// Préparer les données pour les graphiques
$osCount = [];
$browserCount = [];

foreach ($stats as $stat) {
    // Comptage des systèmes d'exploitation
    $os = $stat['os'] ?: 'Inconnu';
    if (!isset($osCount[$os])) {
        $osCount[$os] = 0;
    }
    $osCount[$os]++;

    // Comptage des navigateurs
    $browser = $stat['browser'] ?: 'Inconnu';
    if (!isset($browserCount[$browser])) {
        $browserCount[$browser] = 0;
    }
    $browserCount[$browser]++;
}

$db->close();
?>

<?php include 'tpl/header.html'; ?>


<style>
    /* Styles pour les graphiques */
    .chart-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 colonnes de largeur égale */
        grid-gap: 20px; /* Espacement entre les graphiques */
        width: 100%; /* Largeur maximale de la page */
    }

    .chart {
        width: 100%; /* Prend toute la place de la cellule */
        height: 400px; /* Hauteur des graphiques */
    }
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
</style>

<!-- Inclusion de bibliothèques JavaScript via JSDelivr -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<a href="link.php" class="button" id="button-back" title="Retour">⬅️</a>

<h2>Statistiques pour le lien : <a href="https://link.Picsou06.fun/<?php echo htmlspecialchars($link['source']); ?>">https://link.Picsou06.fun/<?php echo htmlspecialchars($link['source']); ?></a></h2>

<!-- Graphiques alignés sur une même ligne -->
<div class="chart-container">
    <div>
        <canvas id="osChart" class="chart"></canvas>
    </div>
    <div>
        <canvas id="browserChart" class="chart"></canvas>
    </div>
    <div></div>
    <div>
        <canvas id="visitChart" class="chart"></canvas>
    </div>
    <!-- Ajoutez un autre graphique ici si nécessaire -->
</div>

<script>
// Préparer les données pour le graphique du système d'exploitation
const osData = {
    labels: <?php echo json_encode(array_keys($osCount)); ?>,
    datasets: [{
        label: 'Systèmes d\'Exploitation',
        data: <?php echo json_encode(array_values($osCount)); ?>,
        backgroundColor: ['purple', 'Orange', 'Blue', 'Green', 'Yellow'], // Couleurs assignées
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

// Préparer les données pour le graphique des navigateurs
const browserData = {
    labels: <?php echo json_encode(array_keys($browserCount)); ?>,
    datasets: [{
        label: 'Navigateurs',
        data: <?php echo json_encode(array_values($browserCount)); ?>,
        backgroundColor: ['purple', 'Orange', 'Blue', 'Green', 'Yellow'], // Couleurs assignées
        borderColor: 'rgba(153, 102, 255, 1)',
        borderWidth: 1
    }]
};
// Préparer les données pour le graphique des visites par jour
const visitData = {
    labels: <?php echo json_encode(array_keys($visitCounts)); ?>,
    datasets: [{
        label: 'Nombre de visites',
        data: <?php echo json_encode(array_values($visitCounts)); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};

// Créer le graphique des visites par jour
const visitCtx = document.getElementById('visitChart').getContext('2d');
const visitChart = new Chart(visitCtx, {
    type: 'bar',
    data: visitData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Créer le graphique du système d'exploitation
// Créer le graphique du système d'exploitation
const osCtx = document.getElementById('osChart').getContext('2d');
const osChart = new Chart(osCtx, {
    type: 'doughnut', // Changez en 'bar' ou 'doughnut' si vous préférez
    data: osData,
    options: {
        responsive: true, // Rendre le graphique réactif
        maintainAspectRatio: false // Permet de changer le rapport d'aspect
    }
});

// Créer le graphique des navigateurs
const browserCtx = document.getElementById('browserChart').getContext('2d');
const browserChart = new Chart(browserCtx, {
    type: 'doughnut', // Changez en 'bar' ou 'doughnut' si vous préférez
    data: browserData,
    options: {
        responsive: true, // Rendre le graphique réactif
        maintainAspectRatio: false // Permet de changer le rapport d'aspect
    }
})
</script>

<?php if (!empty($stats)) { ?>
    <table>
        <tr>
            <th>Date</th> <!-- Colonne pour la date -->
            <th>Heure</th> <!-- Colonne pour l'heure -->
            <th>Adresse IP</th>
            <th>Localisation</th>
            <th>Navigateur</th>
            <th>Système d'Exploitation</th>
        </tr>
        <?php foreach ($stats as $stat) { ?>
            <tr>
                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($stat['day']))); ?></td> <!-- Affichage de la date -->
                <td><?php echo htmlspecialchars($stat['time']); ?></td> <!-- Affichage de l'heure -->
                <td><?php echo htmlspecialchars($stat['ip']); ?></td>
                <td><?php echo htmlspecialchars($stat['location']); ?></td>
                <td><?php echo htmlspecialchars($stat['browser']); ?></td>
                <td><?php echo htmlspecialchars($stat['os']); ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <p>Aucune statistique disponible pour ce lien.</p>
<?php } ?>

<?php include 'tpl/footer.html'; ?>