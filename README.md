SallyPHP
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapide à prendre en main afin de créer des applications riches et structurées.

## Hello world!

L'exemple fournit dans les sources est configuré pour fonctionner depuis l'adresse *http://127.0.0.1/sallyphp/* en admettant que le repertoire sallyphp se trouvent dans le répertoire */var/www/*.

Exemple de configuration Apache :

    Alias /sallyphp "/var/www/sallyphp"
    <Directory "/var/www/sallyphp">
        SetEnv environnement local
    </Directory>