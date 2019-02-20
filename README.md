# Yomli Go

![logo](./tpl/go.png)

Raccourcisseur d’URL minimaliste basé sur [Go!](http://warriordudimanche.net/article720/go-un-raccourcisseur-d-url). Vous pouvez voir [une démo de la première version](http://go.warriordudimanche.net/) sur le site de Bronco.

> **English:** Yomli Go is an url shortener written in PHP. It requires no database, not even SQLite (links are stored in an encrypted PHP file). I just wanted to have pretty much all the good features of [Lstu](https://lstu.fr/) in less than 100KB of PHP, for a personal server. See the [FAQ in English](#faq-english).

![Yomli Go index](https://justpaste.it/img/ed3604e3307282e5ef49a3b1a7deb908.jpg)

## Nouvelles fonctionnalités

- Intégration des QRCodes envoyés par une API externe.
- Bouton « Copier dans le presse-papier » (nécessite javascript).
- Interface d'administration tout-en-un : importer/exporter au format json, copier des liens, voir les QRCodes associés, purger l'ensemble des URL…
![Importation](https://justpaste.it/img/f7c4ccba22db0f48d14c613c137f8acf.jpg)
- Sécurisation avec [auto_restrict](https://github.com/broncowdd/auto_restrict) avec possibilité de choisir quelle partie rendre privée : la suppression des liens, l'ajout, les deux, voire totalement ouvert. Dans tous les cas, la redirection est accessible à tout le monde.
- Réorganisation des fichiers.
- Fichier de configuration dans le même répertoire que la base, permettant une sauvegarde simplifiée.
![Importation](https://justpaste.it/img/ed71d51d5a37d56f0c87e7520d259480.jpg)
- Les URL peuvent être au format mydomain.tld/go/*code* en plus de mydomain.tld/go/?*code* (.htaccess, demande un serveur Apache).
- Nouveau logo.

### Demonstration

Vous pouvez essayez les fonctionnalités utilisateurs sur la [demo](https://apps.yom.li/go).

## Installation

Récupérez [l'archive](https://github.com/yomli/yomli-go/archive/master.zip), dézippez-la sur votre serveur, où vous voulez.

### Pré-requis

#### Serveur

Logiciels :
- PHP 5.6+
- < 500 Ko d'espace disque (si l'on compte large)

#### Client

- Javascript pour la copie dans le presse-papier (optionnel)
- Cookies pour la connexion à l'administration

### Première visite
Connectez-vous à la page `admin.php`, que ce soit à partir du lien en haut à droite, ou directement par l'URL. Lors de la première visite, on vous demandera de remplir un identifiant et un mot de passe.

### Configuration
Modifiez le fichier `data/config.php` (ou pas, ça fonctionne très bien sans y toucher). Deux paramètres, pour le moment.

#### Privatiser
```php
$privateGo = false;	// Le mot de passe sera demandé pour l'interface d'administration seulement
$privateGo = true;  // L'ajout de lien demande un mot de passe également
//$privateGo        // Commenter cette variable désactive toute protection, y compris celle de l'administration
```

#### Le pourvoyeur de QRCode
```php
$qrcodeAPI = "http://domain.tld/qrcode/?size=4&url=";	// L'adresse de l'API générant le QRCode
$privateGo = "";    // QRCode désactivés
```

## FAQ

### J'ai perdu mon mot de passe !
Pas de panique ! Supprimez simplement le dossier `core/auto_restrict/auto_restrict_files`, et reconnectez-vous à la page `admin.php`. Elle vous permettra de recréer un compte.

### English
<a id="faq-english" />
I don't have time to translate this. There are bits of French pretty much everywhere. If you want to help to translate that into your language I will see what I can do to integrate. The PHP code is straightforward, and easily understood, though. It's not a super duper project, just a good-enough app, modified "à l'arrache".

## Ressources utilisées
- [Go!](http://warriordudimanche.net/article720/go-un-raccourcisseur-d-url), par Bronco, sous licence [FaisCeQueTuVeuxMaGueule](http://www.wtfpl.net/).
- [auto_restrict](https://github.com/broncowdd/auto_restrict), par Bronco.

## Licence
Publié sous licence [WTFPL](http://www.wtfpl.net/). Voir le fichier [LICENSE](LICENSE) pour plus de détails.
