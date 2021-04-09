# ssh-manager WIP
Backend um SSH Keys auf Server zu Deployen sowie zu entfernen.

Eine Selfservice Funktion für den Enduser zum Ändern seines Keys oder zur Anfrage, das er Zugriff auf einen Server haben möchte sind noch geplant.

:warning:  `ssh-keygen -m PEM -t rsa` benutzen um die Keys für den Manager zu erstellen, sonst kann es zu PHP Fehlern kommen.

## Installation
Projekt einrichten, [composer](https://getcomposer.org/), [npm](https://www.npmjs.com/get-npm), [yarn](https://classic.yarnpkg.com/en/docs/install) werden vorrausgesetzt.
```shell
composer install
npm install
yarn encore dev
```


Getestet mit einer [Homestead Vagrantbox](https://laravel.com/docs/8.x/homestead)
```shell
vagrant up
vagrant ssh

sudo apt update && sudo apt install php-ssh2
```
in das Verzeichnis wechseln und dort die Datenbank einrichten (ggfs. vorher die .env Datei anpassen)
```shell
php bin\console doctrine:schema:create --force
```

SSH Keys für die Anwendung erstellen (müssen vom PHP/Apache/nginx User gelesen werden können):
```shell
mkdir ssh
ssh-keygen -m PEM -t rsa
```

Im Admin Backend `URL/admin` kann eine Gruppe und danach ein User erstellt werden.

