SallyPHP
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapide à prendre en main afin de créer des applications riches et structurées.

## Hello world!

Caractéristes de l'exemple fournit avec les sources :

- Structure HMVC (répertoire modules, contenant plusieurs sous structures MVC)
- Près à être utilisé en local (http://127.0.0.1/sallyphp/index.php)

Configuration Apache :

    Alias /sallyphp "/var/www/sallyphp/public"
    <Directory "/var/www/sallyphp/public">
        SetEnv environnement local
    </Directory>