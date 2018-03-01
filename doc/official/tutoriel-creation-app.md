Tutoriel de création d'une application simple avec kamille
==========
2018-03-01


Aujourd'hui nous allons voir comment créer une application de base avec le framework kamille.

Pour commencer, on va importer une architecture de base.


Allez ici et téléchargez le contenu dans votre application.
Alternativement, vous pouvez utiliser l'outil git:

```bash
git clone ...
```





Mise en place du serveur web
--------------------------------
Le choix du serveur web dépend de vous.
Pour le reste de ce tutoriel, j'utiliserai apache.


### Configuration des vhosts avec MAMP

```bash
open /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```

Mon virtual host:

```apacheconfig
<VirtualHost *:80>
    ServerAdmin admin@gmail.com
    DocumentRoot "/myphp/kamille-app/www"
    ServerName kamille-app
    SetEnv APPLICATION_ENVIRONMENT dev
    <Directory "/myphp/kamille-app/www">
        AllowOverride All
    </Directory>
</VirtualHost>
```


Ne pas oublier le host:

```bash
open /private/etc/hosts
```

Et ajouter la ligne:

```bash
127.0.0.1		kamille-app
```

Enfin relancer MAMP dans cet exemple.

Ouvrir le navigateur sur **http://kamille-app** pour vérifier.





Theme, only with renderClaws (not required before)


Lnc1: (sandwich)
https://github.com/lingtalfi/layout-naming-conventions#lnc_1


nommage: https://github.com/lingtalfi/laws (.tpl.php)

controller: possible to put all methods in one controller, or create one controller per method, I'm used to the 
second way but it's up to you.


uni import -f Bat training...