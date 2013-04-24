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


Sally_Acl
---------

**Récupérer l'instance**

    $acl = Sally_Acl::getInstance();

**Ajouter des rôles**

    $acl->addRole('guest');
    $acl->addRole('user', 'guest');

**Ajouter des ressources**

    $acl->AddRessource('public');
    $acl->AddRessource('account');

**Ajouter des autorisations**

    $acl->allow('guest', 'public');
    $acl->allow('guest', 'account', array('signin', 'signup', 'request'));
    $acl->allow('user', 'account');

**Ajouter une restriction**

    $acl->deny('guest', 'public', array('action_name'));

**Vérifier si un utilisateur a le droit d'accéder à une ressource**

    if (!$acl->isAllowed($role_name, $ressource_name, $action_name)) {
      exit;
    }


Sally_Db
--------

**Récupérer l'instance**

    $db = Sally_Db::getInstance();

**SGBD pris en charges**

- Mysql (avec les API suivantes : PDO)

**Ajouter une connexion à une base de données**

    $db->add(array(
      'type' => 'mysql_pdo',
      'host' => '127.0.0.1',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

**Ajouter d'autres bases de données**

Sally_Db gère les multi-connexions avec PDO. Il suffit d'ajouter le nom de la connexion lors de l'ajout. Par defaut le nom de la connexion est *default*

    $db->add(array(
      'name' => 'principal'
      'type' => 'mysql_pdo',
      'host' => '127.0.0.1',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

    $db->add(array(
      'name' => 'other'
      'type' => 'mysql_pdo',
      'host' => '192.168.1.12',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

**Récupérer une instance de connexion**

Sans argument il vous sera renvoyé la première connexion, *default*.

    $db = Sally_Db::getConnection();

Sinon il suffit de préciser le nom de la connexion.

    $db = Sally_Db::getConnection('other');

**Exemple de requête avec PDO**

    public function getEmail()
    {
      $db = Sally_Db::getConnection();
      $stmt = $db->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
      $stmt->execute(array('id' => 1));
      $result = $stmt->fetch();
      return $result['email'];
    }


Sally_Layout
------------

**Récupérer l'instance**

    $layout = Sally_Layout::getInstance();

**Définir un layout**

En ajoutant un slash devant le nom du layout celui ci sera cherché dans la répertoire *layout* à la racine de l'application. Sinon il sera cherché dans le répertoire *layout* du module demandé par la requête.

    $layout->set('/home');

**Désactiver le layout**

    $layout->disableLayout();

**Vérifier si le layout n'a pas été désactivé**

    $layout->isEnabled();

**Vérifier si un layout est définit**

    $layout->isDefined();