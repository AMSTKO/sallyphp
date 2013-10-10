SallyPHP 
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapides à prendre en main afin de créer des applications riches et structurées.

Points forts
------------

**Les requêtes préparées**

Lorsque vous demandez une page à Sally, la requête est préparée, placée dans un contexte, puis executée. Ce type fonctionnement permet de simuler très simplement d'autres requêtes au coeur de l'application.


**Les trafiquants**

Vous pouvez trafiquer (modifier) une requête à plusieurs niveaux :

- 1. Au début de la requête (vérifier un token, cookie, définir un layout, contrôle ACL, rediriger...);
- 2. Avant la livraison des vues (les envoyer dans un moteur de template...);
- 3. Avant de livrer les actions d'un contrôleur (intégrer le contenu dans un template spécifique au contrôleur...);
- 4. Avant d'intégrer le layout (préparer les variables du layout, charger une vue de menu...);
- 5. Avant de retourner la réponse au client (minifier, ajouter une entête...);

Les cas d'utilisations sont variés.

**HMVC**

Du MVC hiérarchisé permet d'avoir plusieurs structures MVC, séparées en modules, au sein du même projet. Imaginez un module pour le site, un autre pour la version mobile et encore un pour l'api... se partageants les mêmes ressources.

**Clef en main**

Sally est plutôt cool, vous devriez faire beaucoup de chose sans prise de tête. Je vous invite d'ailleurs à regarder les sources et voir comment ça se passe. Vous pouvez facilement modifier le framework pour répondre à des besoins spécifiques.


Sommaire
--------

- [Structure](#structure)
- [Inventaire](#inventaire)
- [Notes](#notes)


- [Sally](#sally)
- [Engine](#engile)
- [Request](#request)


- [Model](#model)
- [View](#view)
- [Controller](#controller)


- [Layout](#layouts)
- [Helper](#helper)
- [Db](#db)
- [Acl](#acl)
- [Session](#session)
- [Trafficker](#trafficker)


- [Rijndael](#rijndael)
- [PHPMailer](#phpmailer)


- [License](#license)

Structure
---------

    application/
      helpers/
      layouts/
      libs/
      models/
      modules/
        api/
          controllers/
        site/
          controllers/
          view/
      traffickers/
    public/
      static/
    sallyphp/


Inventaire
----------

Depuis un contrôleur et un trafiquant vous pouvez accéder aux classes et objets suivants :

    $this->request;
    $this->layout;
    $this->view;
    $this->helper;

    $sally = Sally::getInstance();
    $acl = sally\Acl::getInstance();
    $db = sally\Db::getInstance();
    $session = sally\Session::getInstance();
    $rijndael = sally\Rijndael::getInstance();


Notes
-----

**slash devant éléments à charger**

    // par exemple :
    echo $this->view->load('/ma-vue');
    // ou
    $engine->helper->add('/translate');


En ajoutant un slash devant le chemin d'un élément à charger (helper, view ou layout) celui ci sera cherché dans son répertoire à la racine de l'application. Sinon il sera cherché dans son répertoire depuis le module demandé par la requête.


Sally
-----

La classe Sally s'occupe des requêtes, de la configuration, du chargement des fichiers et autres classes.

**Récupérer l'instance**

    $sally = Sally::getInstance();

**Faire une requête**

    // il faut d'abord préparer la requête, cette méthode retourne 
    // un objet "singleton" de sally\Engine();, content d'autres 
    // objets spécifiques à la requête :
    $engine = $sally->prepare($_SERVER['REQUEST_URI']);

    // vous pouvez ensuite activer des trafiquants :
    $engine->trafficker->add('site', array('site'));
    $engine->trafficker->add('api', array('api'));

    // charger des helpers :
    $engine->helper->add('/translate');
    $engine->helper->add('/beautifulDate');

    // faire évoluer la configuration en fonction de la requête :
    if ($engine->request->getModule() == 'site') {
      $engine->helper->add('mustache');
      $engine->helper->add('escape');
      $engine->helper->add('api');
    }

    // et finir par executer puis afficher le résultat :
    echo $engine->execute();

**Définir un paramètre global**

Vous pouvez définir des valeurs qui seront accessibles depuis n'importe ou (vue, layout, trafiquant, helper...).

    Sally::set('name', 'Pingu');
    // il est possible d'avoir des paramètres enfants
    Sally::set('user', 'id', 6);

**Récupérer un paramètre global**

    Sally::get('name'); // Pingu
    // or
    Sally::get('user'); // array('id' => 6);
    // or
    Sally::get('user', 'id'); // (int)6

**Charger une librairie**

Pour l'instant il s'agit d'un simple "require_once" sur le fichier qui vous intéresse dans votre répertoire "libs", par exemple :

    $sally->library('Mustache/Autoloader.php');
    $sally->library('Predis/autoload.php');


Engine
------

La classe Engine est instanciée à chaque nouvelle requête, elle va donner un "singleton" qui contiendra les différents éléments dédiés à une requête.

    // depuis un trafiquant et un contrôleur vous pourriez accédez à ses objets de cette manière,
    $this->engine->request;
    $this->engine->layout;
    $this->engine->view;
    $this->engine->helper;

    // mais ils ont leur version raccourci,
    $this->request;
    $this->layout;
    $this->view;
    $this->helper;


Model
-----

En structure MVC ou si en HMVC votre model se trouve dans son repertoire à la racine de l'application son nom ressemblera à : "UserModel". En HMVC avec un model présent dans son repertoire au niveau du module il faudrai ajouter le nom du module devant : "Site_UserModel".

    class UserModel extends sally\Model
    {
      public function signin()
      {
        // ...
      }
    }


View
----

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

**Désactiver la vue par defaut d'une action de contrôleur**

    $this->view->disableControllerView();

**Savoir si la vue par defaut été désactivé**

    $this->view->controllerViewIsEnabled(); // boolean


Controller
----------

En structure MVC le nom du contrôleur est sous cette forme : "IndexController". En HMVC il faudra ajouter le nom du module devant : "Site_IndexController"

**__contruct**

Si vous ajoutez votre méthode __contruct au contrôleur il faudra faire appel manuellement au constructeur parent, sans oublier de transmettre l'objet $engine :

    class Site_IndexController extends sally\Controller
    {
      public function __construct($e)
      {
        parent::__construct($e);
      }
    }

**Charger un helper**

    $this->helper->add('/toStrong');
    
**Redirection client**

    $this->redirect('http://google.fr');

**Redirection interne**

Rediriger vers une autre action et/ou un autre controleur et/ou un autre model dans la même requête

    $this->forward($action, $controleur, $module);

Il est nécessaire de préciser au moins l'action (le contrôleur et le module seront ceux en cours). Exemple :

    class Site_IndexController extends sally\Controller
    {
      public function index()
      {
        $this->forward('maintenance', 'index');
      }
    }


Layout
------

**Définir un layout**

    $this->layout->set('/home');

**Désactiver le layout**

    $this->layout->disableLayout();

**Vérifier si le layout n'a pas été désactivé**

    $this->layout->isEnabled(); // Boolean

**Vérifier si un layout est définit**

    $this->layout->isDefined(); // Boolean

**Transmettre des variables dans le layout**

    $this->layout->setData('name1', 'value1');

    // or

    $layout->setData(array(
      'name1' => 'value1',
      'name2' => 'value2'
    ));

    // in view file : echo $name1; // display value1

**Récupérer des variables transmises au layout**

  $layout->getData('name1'); // return value1


Acl
---

**Récupérer l'instance**

    $acl = sally\Acl::getInstance();

**Ajouter des rôles**

    $acl->role('guest');
    $acl->role('user', 'guest');

**Ajouter des ressources**

    $acl->ressource('public');
    $acl->ressource('account');

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

    $db = sally\Db::getInstance();

**SGBD pris en charges**

- Mysql (avec PDO), type=mysql-pdo
- Redis (avec la librairie Predis), type=redis-predis

**Ajouter une connexion à une base de données**

Pour ne pas avoir besoin de préciser le nom à chaque fois avec getConnection('nom'); vous pouvez indiqur le nom de connexion "default".

    $db->add(array(
      'type' => 'mysql-pdo',
      'name' => 'default',
      'host' => '127.0.0.1',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

**Récupérer une instance de connexion**

Sans argument il vous sera renvoyé la première connexion, *default*.

    $db = sally\Db::getConnection();

Sinon il suffit de préciser le nom de la connexion.

    $db = sally\Db::getConnection('other');

**Exemple de requête avec PDO dans un model**

    public function getEmail($user)
    {
      $db = sally\Db::getConnection();
      $stmt = $db->prepare('SELECT email FROM users WHERE id =:id LIMIT 1');
      $stmt->execute(array('id' => $user));
      $result = $stmt->fetch();
      return $result['email'];
    }


Request
-------

Une requête se compose de 2 parties :

- 1. Le chemin : /module/controller/action
- 2. Les données : ?user_name=pingu&page=2

Par exemple : domain.com/api/user/name?id=6

**Récupérer les valeurs des données passées dans la requête**

    $this->request->getSegment('dataName1'); // False si inexistante

**Écraser des valeurs passées dans la requête**

    $this->request->setSegment('dataName1', 'dataValue1');

**Récupérer des données $_POST**

    $this->request->getData('name'); // value or false

**Redéfinir le module**

    $this->request->setModule('module_name');

**Redéfinir le controleur**

    $this->request->setController('controller_name');

**Redéfinir l'action**

    $this->request->setAction('action_name');

**Récupérer le nom du module en cours**

    $this->request->getModule();

**Récupérer le nom du controleur en cours**

    $this->request->getController();

**Récupérer le nom de l'action en cours**

    $this->request->getAction();


Session
-------

Sally créer un cookie dont la valeur est cryptée avec l'algo Rijndael en 128b (MCRYPT_RIJNDAEL_128). La valeur du cookie correspond à un tableau sérialisé contenant vos données.

**Récupérer l'instance**

    $session = sally\Session::getInstance();

**Savoir si l'utilisateur avait déjà le cookie**

    $session->hasCookie(); // boolean

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

Les helpers sont des fichiers contenants une ou plusieurs fonctions PHP (ou ce que vous voullez) appelable n'importe ou.

**Charger un helper**

    $this->helper->add('toStrong'); // helper name

**Exemple de helper : toStrongHelper.php**
    
    <?php
    function toStrong($text)
    {
      echo '<strong>' . $text . '</strong>';
    }


Trafficker
----------

Le trafiquant permet d'agir à 5 endroits :

- preEngine : Appelée au début de la requête;
- viewDelivery : Appelée avant la livraison de la vue;
- preLayout : Appelée avant d'intégrer le contenu au layout;
- engineDelivery : Appelée avant de retourner le contenu de la réponse au client;

**preEngine**

Intercepter la requête au début du traitement.

- redéfinir le nom du module, du controleur ou de l'action pour afficher une autre page que prévu;
- vérifier les droits ACL et faire un choix d'affichage ou redirection à ce moment la;
- définir un layout en fonction de l'utilisateur;
- afficher une page d'erreur;
- ...

**viewDelivery**

Si vous avez un moteur de template à executer sur le contenu des vues.

  function viewDelivery($content, $data)
  {
    $m = new Mustache_Engine;
    return $m->render($content, $data);
  }

**preLayout**

Utiliser par exemple pour définir des variables au template du layout avec : $this->layout->setData();

**engineDelivery**

Trafiquer le retour de la requête au dernier moment.

- ajouter une token;
- ajouter une information (temps de traitement...);
- ...

**Charger un trafiquant**

    $engine->trafficker->add('my');


Rijndael
--------

**Récupérer l'instance**

    $rijndael = sally\Rijndael::getInstance();

**Définir une clef de cryptage**

    $rijndael->setKey('your key');

**Crypter des données**

    $rijndael->encrypt('data');

**Décrypter des données**

    $rijndael->decrypt('dataCrypted');


PHPMailer
---------

Pour d'avantage de documentation rendez-vous sur https://github.com/Synchro/PHPMailer

**Charger la librairie**

    $sally->library('PHPMailer/PHPMailer.php');
    $PHPMailer = new PHPMailer();

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