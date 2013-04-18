SallyPHP
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapide à prendre en main afin de créer des applications riches et structurées.

## Hello world!

L'exemple fournit dans les sources est configuré pour fonctionner depuis l'adresse *http://127.0.0.1/sallyphp* en admettant que le repertoire sallyphp se trouve dans le répertoire */var/www/*.

Configuration Apache :

    Alias /sallyphp "/var/www/sallyphp/public"
    <Directory "/var/www/sallyphp/public">
        SetEnv environnement local
    </Directory>


Essayez d'accéder à *http://127.0.0.1/sallyphp* et *http://127.0.0.1/sallyphp/admin*

Pour un accès en ligne de commande vous pouvez essayer : 

  php cli.php /controller/action