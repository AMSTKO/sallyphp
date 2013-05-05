SallyPHP
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapides à prendre en main afin de créer des applications riches et structurées.

## Hello world!

Caractéristes de l'exemple fournit avec les sources :

- Structure HMVC (répertoire modules, contenant plusieurs sous structures MVC)
- Près à être utilisé en local (http://127.0.0.1/sallyphp/index.php/index)

Configuration Apache :

    Alias /sallyphp "/var/www/sallyphp/public"
    <Directory "/var/www/sallyphp/public">
        SetEnv environnement local
    </Directory>

Sommaire
--------

- [Structure](#structure)
- [Inventaire](#inventaire)
- [Notes](#notes)
- [Sally](#sally)
- [Controller](#controller)
- [View](#view)
- [Layout](#layouts 
- [Acl](#acl)
- [Db](#db)
- [Request](#request)
- [Helper](#helper)
- [Session](#session)
- [Trafficker](#trafficker)
- [Rijndael](#rijndael)
- [PHPMailer](#phpmailer)
- [License](#license)

Structure
---------

    application/
      helpers/
      layouts/ (templates)
      models/
      modules/
        admin/
          controllers/
          view/
        cli/
          controllers/
        site/
          controllers/
          view/
      traffickers/
    public/
      static/ (img, js, css...)
    sallyphp/ (class of Sally)


Inventaire
----------

Liste des class auxquelles vous pourrez avoir besoin au cours de votre développement.

    $sally = Sally::getInstance();
    $acl = Acl::getInstance();
    $db = Db::getInstance();
    $request = Request::getInstance();
    $layout = Layout::getInstance();
    $helper = Helper::getInstance();
    $session = Session::getInstance();
    $trafficker = Trafficker::getInstance();

**Divers**

    $rijndael = Rijndael::getInstance();
    $PHPMailer = PHPMailer::getInstance();

Notes
-----

**slash devant éléments à charger**

En ajoutant un slash devant le nom d'un élément à charger (helper, view ou layout) celui ci sera cherché dans son répertoire à la racine de l'application. Sinon il sera cherché dans son répertoire depus le module demandé par la requête.


Sally
-----

**Récupérer l'instance**

    $sally = Sally::getInstance();

**Récupérer et écraser la réponse de la requête**

*Retourner le contenu qui sera envoyé au navigateur :*

    $sally->getOut();

*Écraser ce contenu pour renvoyer autre chose :*

    $sally->setOut('your content...');

Exemple d'utilisation : Dans un *trafficker*, avant de délivrer le contenu au navigateur, vous pourriez retirer tous les espaces d'indendations pour économiser de la bande passante.

    class MyTrafficker extends TraffickerAbstract
    {
      function preDelivery()
      {
        $sally->setOut(preg_replace('/\s\s+/', ' ', $sally->getOut());
      }
    }

**Récupérer un retour de controleur**

Les controleurs ont la possibilité de retourner des valeurs. Vous pourriez récupérer ces valeurs dans un *trafficker* pour les modifier (ajouter un token... exemple disponible sur la documentation du trafficker).

    $sally->getDataBack();


Controller
----------

**__contruct**

Si vous ajoutez votre méthode __contruct au controleur alors il faudra faire référence au contructeur parent :

    class IndexController extends Controller
    {
      public function __construct()
      {
        parent::__construct();
      }
    }

**Transmettre des variables dans la vue principale**

    $this->view->setData('name1', 'value1');

    // or

    $this->view->setData(array(
      'name1' => 'value1',
      'name2' => 'value2'
    ));

    // in view file : echo $name1; // display value1

**Charger une vue**

    echo $this->view->load('/sidebar', array(
      'login' => 'Mr.Ping'
    ));

    // in view file : echo $login; // display Mr.Ping

**Charger un helper**

    $this->helper('/tostrong');

**Redirection**

    $this->redirect('http://google.fr');

**Rediriger vers une autre action dans la même requête**

    $this->forward($action, $controleur, $module);

Il est nécessaire de préciser au moins l'action (controleur et module seront ceux en cours). Exemple :

    class IndexController extends Controller
    {
      public function index()
      {
        $this->forward('maintenance', 'erreur');
      }
    }

En accédant à l'index il y aura une redirection transparente vers l'action "maintenance" du controleur "erreur".


View
----

**Récupérer l'instance**

    $view = View::getInstance();

**Désactiver l'appel automatique d'une vue pour l'action du controleur**

    $view->disableControllerView();

**Vérifier si l'appel automatique d'une vue n'a pas été désactivé**

    $view->controllerViewIsEnabled(); // Boolean


Layout
------

**Récupérer l'instance**

    $layout = Layout::getInstance();

**Définir un layout**

    $layout->set('/home');

**Désactiver le layout**

    $layout->disableLayout();

**Vérifier si le layout n'a pas été désactivé**

    $layout->isEnabled(); // Boolean

**Vérifier si un layout est définit**

    $layout->isDefined(); // Boolean

**Transmettre des variables dans le layout**

    $>layout->setData('name1', 'value1');

    // or

    $layout->setData(array(
      'name1' => 'value1',
      'name2' => 'value2'
    ));

    // in view file : echo $name1; // display value1


Acl
---

**Récupérer l'instance**

    $acl = Acl::getInstance();

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


Db
--

**Récupérer l'instance**

    $db = Db::getInstance();

**SGBD pris en charges**

- Mysql (avec PDO)

**Ajouter une connexion à une base de données**

    $db->add(array(
      'type' => 'mysql_pdo',
      'host' => '127.0.0.1',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

**Ajouter d'autres bases de données**

DbSally gère les multi-connexions avec PDO. Il suffit d'ajouter le nom de la connexion lors de l'ajout. Par defaut le nom de la connexion est *default*

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

    $db = Db::getConnection();

Sinon il suffit de préciser le nom de la connexion.

    $db = Db::getConnection('other');

**Exemple de requête avec PDO**

    public function getEmail($user)
    {
      $db = Db::getConnection();
      $stmt = $db->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
      $stmt->execute(array('id' => $user));
      $result = $stmt->fetch();
      return $result['email'];
    }


Request
-------

Les requêtes peuvent être faites sous différentes formes :

- /module/controller/action
- /controller/action (en définissant le module par defaut dans la conf)
- /controller/action/dataName1/dataValue1/dataName2/dataValue2

**Récupérer l'instance**

    $request = Request::getInstance();

**Récupérer les valeurs des données passées dans la requête**

    $request->getSegment('dataName1'); // False si inexistante

**Écraser des valeurs passées dans la requête**

    $request->setSegment('dataName1', 'dataValue1');

**Récupérer des données $_POST**

    $request->getPost('name'); // value or false

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


Session
-------

Sally créer un cookie dont la valeur est cryptée avec l'algo Rijndael en 128b (MCRYPT_RIJNDAEL_128). La valeur correspond à un tableau sérialisé contenant vos informations.

**Récupérer l'instance**

    $session = Session::getInstance();

**Savoir si l'utilisateur avait déjà le cookie**

    $session->hasCookie(); // Boolean

**Définir une valeur dans le cookie**

    $session->set('logged', 1);

**Récupérer une valeur du cookie**

    $session->get('logged');

**Récupérer le tableau contenant toutes les valeurs du cookie**

    $session->getContent();

**Écraser tous le contenu du cookie**

    $session->setContent();

    // ou

    $session->setContent(array(
      'logged' => 1,
      'username' => 'Pingoo'
    ));


Helper
------

Les helpers sont de basiques fonctions PHP appelable n'importe ou.

**Récupérer l'instance**

    $helper = Helper::getInstance();

**Charger un helper**

    $helper->load('helper_name');

**Exemple de helper : toStrongHelper.php**
    
    <?php
    function toStrong($text)
    {
      echo '<strong>' . $text . '</strong>';
    }


Trafficker
----------

Le trafiquant permet d'agir à 2 endroits :

- avant l'appel d'un controleur;
- avant de retourner le contenu de la requête;

**avant l'appel d'un controleur : preDeal() {}**

Intercepter la requête au début du traitement.

- redéfinir le nom du module, du controleur ou de l'action pour afficher une autre page que prévu;
- vérifier les droits ACL et faire un choix d'affichage ou redirection à ce moment la;
- définir un layout en fonction de l'utilisateur;
- afficher une page d'erreur;
- ...

**avant de retourner le contenu de la requête : preDelivery() {}**

Trafiquer le retour de la requête au dernier moment.

- ajouter une token;
- ajouter une information (temps de traitement...);
- ...

**Récupérer l'instance**

    $trafficker = Trafficker::getInstance();

**Charger un trafiquant**

    $trafficker->add('my');

**Exemple de trafiquant : MyTrafficker.php**

Je vais avoir beaucoup de requêtes ajax sur mon projet. Alors je décide que chaque controleur aura une action nommée "request" qui permettra de traiter ces requêtes. Dans un premier temps (preDeal) on désactive le layout et la vue par defaut pour l'action "request". Une fois la requête prête à être renvoyée (preDelivery) on ajoute des valeurs (ici un token).

    class MyTrafficker extends TraffickerAbstract
    {
      function __construct()
      {
        $this->layout = Layout::getInstance();
        $this->view = View::getInstance();
        $this->request = Request::getInstance();
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
        if ($this->request->getAction() == 'request') {
          $sally = Sally::getInstance();
          $sally->setOut(json_encode(array_merge(array(
            'token' => 12456
          ), $sally->getDataBack())));
        }
      }
    }


Rijndael
--------

**Récupérer l'instance**

    $rijndael = Rijndael::getInstance();

**Définir une clef de cryptage**

    $rijndael->setKey('your key');

**Crypter des données**

    $rijndael->encrypt('data');

**Décrypter des données**

    $rijndael->decrypt('dataCrypted');


PHPMailer
---------

Pour d'avantage de documentation rendez-vous sur https://github.com/Synchro/PHPMailer

**Récupérer l'instance**

    $PHPMailer = PHPMailer::getInstance();

**Configuration**

    $PHPMailer->IsSMTP();
    $PHPMailer->Host = 'in.mailjet.com';
    $PHPMailer->Port = 587;
    $PHPMailer->SMTPAuth = true;
    $PHPMailer->Username = 'username';
    $PHPMailer->Password = 'password';
    $PHPMailer->SMTPSecure = 'tls';

**Envoyer un e-mail**

    $PHPMailer->From = 'from@example.com';
    $PHPMailer->AddAddress('ellen@example.com');
    $PHPMailer->IsHTML(true);
    $PHPMailer->Subject = 'Here is the subject';
    $PHPMailer->Body    = 'This is the HTML message body <b>in bold!</b>';
    $PHPMailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $PHPMailer->Send(); // Boolean


License
-------

**New BSD License**

Copyright (c) 2013, Jonathan Amsellem.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of Jonathan Amsellem nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.


THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.