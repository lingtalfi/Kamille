Modules
==========
2018-03-02



Les modules que j'ai créés sont pour l'instant tous ici: https://github.com/KamilleModules/



Installation d'un module
----------------


### Installation manuelle

L'installation manuelle est pour l'instant la seule méthode recommandée.

Elle se compose de deux étapes:

- import des fichiers du module dans le dossier **class-modules**
- ajout du nom du module dans le fichier **modules.txt**




### L'installateur de modules

Il s'agit d'un code expérimental dont le but est d'installer les modules automatiquement.
Son action comptait entre autres:

- la migration des fichiers du module vers l'application cible
- l'inscription automatique des hooks d'un module
- l'exécution de diverses tâches d'installation du module, telle que la création de base de données


L'installateur de modules existe à l'état latent dans le code du framework Kamille, mais a été abandonné
en pratique, car l'expérience a montré que l'installation manuelle des modules était plus pratique pendant
la phase de développement du framework.

Il est toutefois possible qu'à l'avenir cet outil soit remis sur pied.




