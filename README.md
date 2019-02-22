<h1 align="center">
	<br>
	<a href="https://apps.yom.li/go/">
		<img src="./tpl/go.png" alt="Yomli Go" width="192">
	</a>
	<br>
	Yomli Go
	<br>
</h1>

<h4 align="center">Raccourcisseur d’URL minimaliste basé sur <a href="http://go.warriordudimanche.net/" target="_blank">Go!</a></h4>

<p align="center">
  <a href="#features">Fonctionnalités</a> →
  <a href="#install">Installation</a> →
  <a href="#config">Configuration</a> →
  <a href="#faq">FAQ</a> →
  <a href="#credits">Crédits</a> →
  <a href="#license">Licence</a>
</p>

![screenshot](https://apps.yom.li/go/screenshots/screen1.png)

> **English:** Yomli Go is an url shortener written in PHP. It requires no database, not even SQLite (links are stored in an encrypted PHP file). I just wanted to have pretty much all the good features of [LSTU](https://lstu.fr/) in less than 50KB of PHP, for a personal server. See the [FAQ in English](#faq-english).



## Nouvelles fonctionnalités
<a id="features" />

- Intégration des QRCodes envoyés par une API externe.
- Bouton « Copier dans le presse-papier » (nécessite javascript).
- Interface d'administration tout-en-un :
	* Importer/exporter au format JSON.
	* Copier des liens simplement.
	* Voir les QRCodes associés.
	* Purger l'ensemble des URL…
- Sécurisation avec [auto_restrict](https://github.com/broncowdd/auto_restrict).
	* Possibilité de choisir quelle partie rendre privée : la suppression des liens, l'ajout, les deux, voire totalement ouvert. 
	* Dans tous les cas, la redirection est accessible à tout le monde.
- Interface de gestions des liens utilisateur :
	* Importer/exporter au format JSON.
	* Afficher les liens stockés dans le navigateur.
- Réorganisation des fichiers.
- Fichier de configuration dans le même répertoire que la base, permettant une sauvegarde simplifiée.
- Les URL peuvent être au format `mydomain.tld/go/code` en plus de `mydomain.tld/go/?code` (.htaccess, demande un serveur Apache).
- Appel externe par requête `GET` : `https://mydomain.tld/?url=http://long.url&code=foobar`.
- Nouveau logo.

![screenshot](https://apps.yom.li/go/screenshots/screen2.png)

## Installation
<a id="install" />

Récupérez [l'archive](https://github.com/yomli/yomli-go/archive/master.zip), dézippez-la sur votre serveur, où vous voulez.

### Pré-requis

#### Serveur

- Apache (optionnel, juste pour la redirection des GET)
- PHP 5.6+
- < 500 Ko d'espace disque (si l'on compte large)

#### Client

- Javascript pour la copie dans le presse-papier (optionnel)
- Cookies pour la connexion à l'administration
- Navigateur supportant `localStorage` et `File API` pour le stockage des liens (optionnel)

### Première visite
Connectez-vous à la page `admin.php`, que ce soit à partir du lien en haut à droite, ou directement par l'URL. Lors de la première visite, on vous demandera de remplir un identifiant et un mot de passe.

## Configuration
<a id="config" />

Modifiez le fichier `data/config.php` (ou pas, ça fonctionne très bien sans y toucher). Deux paramètres, pour le moment.

### Sécuriser
```php
# Mot de passe sur l'administration seulement
$privateGo = false;	

# Mot de passe sur l'ajout de liens
$privateGo = true;

# Commenter pour désactiver toute protection
//$privateGo
```

### URL sans `?`
```php
# Liens sous la forme
# mydomain.tld/code
$stripQuestion = true;	
```
Nécessite l'emploi du `.htaccess` fourni.

### QRCodes
```php
# L'adresse de l'API générant les QRCodes
$qrcodeAPI = "http://domain.tld/qrcode/?size=4&url=";

# QRCodes désactivés
$qrcodeAPI = "";
```
Vous pouvez héberger votre propre API en installant [qrcode-url](https://github.com/timovn/qrcode-url) de Timo Van Neerden.

## FAQ
<a id="faq" />

### J'ai perdu mon mot de passe !
Pas de panique ! Supprimez simplement le dossier `core/auto_restrict/auto_restrict_files`, et reconnectez-vous à la page `admin.php`. Elle vous permettra de recréer un compte.

### Je ne suis pas administrateur, comment retrouver mes liens ?
Les raccourcis créés sont sauvegardés **dans le navigateur**. Si vous en changez, vous devez exporter les liens depuis l'ancien navigateur pour les importer dans le nouveau. Pour ce faire :
- Dans l'ancien navigateur :
    * Cliquez sur le bouton **Mes liens**
    * Cliquez sur **Exporter les URL**
    * Téléchargez le fichier `go-mylinks_[DATE].json`
- Dans le nouveau navigateur :
    * Ouvrez la page **Mes liens**
    * Cliquez sur **Parcourir**
    * Sélectionnez le fichier `go-mylinks_[DATE].json`
    * Cliquez sur **Importer des URL**

### English
<a id="faq-english" />
I don't have time to translate this. There are bits of French pretty much everywhere. If you want to help to translate that into your language I will see what I can do to integrate. The PHP code is straightforward, and easily understood, though. It's not a super duper project, just a good-enough app, modified "à l'arrache".

## Crédits
<a id="credits" />

- [Go!](http://warriordudimanche.net/article720/go-un-raccourcisseur-d-url), par Bronco, sous licence [FaisCeQueTuVeuxMaGueule](http://www.wtfpl.net/).
- [auto_restrict](https://github.com/broncowdd/auto_restrict), par Bronco.

## Licence
Publié sous licence [WTFPL](http://www.wtfpl.net/). Voir le fichier [LICENSE](LICENSE) pour plus de détails.
