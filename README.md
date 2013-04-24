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


Sally_Request
-------------

Les requêtes peuvent être faites sous différentes formes :

- /module/controller/action
- /controller/action (en définissant le module par defaut dans la conf)
- /controller/action/dataName1/dataValue1/dataName2/dataValue2

**Récupérer l'instance**

    $request = Sally_Request::getInstance();

**Récupérer les valeurs des données passées dans la requête**

    $request->getRequest('dataName1'); // False si inexistante

**Redéfinir le module**

    $request->setModule('module_name');

**Redéfinir le controleur**

    $request->setController('controller_name');

**Redéfinir l'action**

    $request->setAction('action_name');

**Récupérer le nom du module en cours**

    $request->getModule();

**Récupérer le nom du controleur en cours**

    $request->getController();

**Récupérer le nom de l'action en cours**

    $request->getAction();


Sally_Layout
------------

**Récupérer l'instance**

    $layout = Sally_Layout::getInstance();

**Définir un layout**

En ajoutant un slash devant le nom du layout celui ci sera cherché dans la répertoire *layouts* à la racine de l'application. Sinon il sera cherché dans le répertoire *layouts* du module demandé par la requête.

    $layout->set('/home');

**Désactiver le layout**

    $layout->disableLayout();

**Vérifier si le layout n'a pas été désactivé**

    $layout->isEnabled(); // Boolean

**Vérifier si un layout est définit**

    $layout->isDefined(); // Boolean


Sally_View
----------

**Récupérer l'instance**

    $view = Sally_View::getInstance();

**Désactiver l'appel automatique d'une vue pour l'action du controleur**

    $view->disableControllerView();

**Vérifier si l'appel automatique d'une vue n'a pas été désactivé**

    $view->controllerViewIsEnabled(); // Boolean


Sally_Session
-------------

Sally_Session est assez specifique à votre projet, vous pouvez jetter un oeil a ce qui a été fait.


Sally_Helper
------------

Les helpers sont de basiques fonctions PHP appelable n'importe ou.

**Récupérer l'instance**

    $helper = Sally_Helper::getInstance();

**Charger un helper**

En ajoutant un slash devant le nom du halper celui ci sera cherché dans la répertoire *helpers* à la racine de l'application. Sinon il sera cherché dans le répertoire *helpers* du module demandé par la requête.

    $helper->load('helper_name');

**Exemple de helper : toStrongHelper.php**
    
    <?php
    function toStrong($text)
    {
      echo '<strong>' . $text . '</strong>';
    }


Sally_Trafficker
----------------

Le trafiquant permet d'agir à 2 endroits :

- avant l'appel d'un controleur;
- avant de retourner le contenu de la requête;

**avant l'appel d'un controleur**

Trafiquer la requête en redéfinissant le nom du controleur (mais aussi bien du module ou de l'action) demandé pour par exemple :

- vérifier les droits ACL et faire un choix d'affichage ou redirection à ce moment la;
- définir un layout en fonction de l'utilisateur;
- afficher une page d'erreur;

**avant de retourner le contenu de la requête**

Trafiquer le retour de la requête au dernier moment.

**Exemple de trafiquant : MyTrafficker.php**

    class MyTrafficker extends Sally_Trafficker_Abstract
    {
      function __construct()
      {
        $this->layout = Sally_Layout::getInstance();
        $this->view = Sally_View::getInstance();
        $this->request = Sally_Request::getInstance();
      }

      function preDeal()
      {
        $this->layout->set('/home');
        if ($this->request->getAction() == 'request') {
          $this->layout->disableLayout();
          $this->view->disableControllerView();
        }
      }

      function preDelivery()
      {
        // je n'ai rien à faire ici
      }
    }