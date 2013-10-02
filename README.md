SallyPHP
========

SallyPHP est un framework permettant de développer des applications web sur les modèles MVC et HMVC (hierarchical model–view–controller). Il fournit des outils simples, légés et rapides à prendre en main afin de créer des applications riches et structurées.

Points forts
------------

**Les requêtes préparées**

Lorsque vous demandez une page à Sally, la requête est préparée, placée dans un contexte, puis executée. Ce fonctionnement permet de simuler très simplement d'autres requêtes au coeur de l'application.

    // si j'ai 2 modules, "site" et "api", je veux faire un appel à l'api depuis le site:
    // public/index.php, traitement de la requête principale,
    $sally = Sally::getInstance();
    $engine = $sally->prepare($_SERVER['REQUEST_URI']);
    echo $engine->execute();

    // dans mon controleur IndexController.php je veux appeler l'api,
    $sally = Sally::getInstance();
    $engine = $sally->prepare('api/user/profile', array('token' => '146446013'));
    $json = $engine->execute();

    // on pourrait très bien faire de ce bout de code un helper ou un model.


**Les trafiquants**

Vous pouvez trafiquer les données qui rentrent et sortent à plusieurs points d'une requête.

- preEngine : Appelée au début de la requête;
- viewDelivery : Appelée avant la livraison de la vue;
- preLayout : Appelée avant d'intégrer le contenu au layout;
- layoutDelivery : Appelée avant la livraison du layout;
- engineDelivery : Appelée avant de retourner le contenu de la réponse au client;

Cas d'utilisation :

- Vérifier un rôle ACL avant de poursuivre;
- Définir un layout;
- Si requête AJAX bloquer la vue par defaut de le layout;
- Supprimer l'indentation du code envoyé au client;
- Rediriger vers une page de maintenance;
- Ajouter des informations globales au retour client (token, temps d'exécution);
- ...

**HMVC**

Du MVC hiérarchisé permet d'avoir plusieurs structures MVC, séparées en modules, au sein du même projet. Imaginez un module pour le site, un autre pour la version mobile et encore un pour l'api... se partageants les mêmes ressources.


Sommaire
--------

- [Structure](#structure)
- [Notes](#notes)
- [Sally](#sally)
- [Controller](#controller)
- [View](#view)
- [Layout](#layouts)
- [Acl](#acl)
- [Db](#db)
- [Request](#request)
- [Helper](#helper)
- [Session](#session)
- [Trafficker](#trafficker)
- [Rijndael](#rijndael)
- [PHPMailer](#phpmailer)
- [License](#license)

Structure principales
---------------------

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

Depuis un traffiquant et un controleur vous pouvez accéder aux objets suivants de la requête :

    $this->request;
    $this->layout;
    $this->view;
    $this->helper;

Ainsi qu'aux classes suivantes :

    $sally = Sally::getInstance();
    $acl = sally\Acl::getInstance();
    $db = sally\Db::getInstance();
    $session = sally\Session::getInstance();
    $rijndael = sally\Rijndael::getInstance();


Notes
-----

**slash devant éléments à charger**

En ajoutant un slash devant le nom d'un élément à charger (helper, view ou layout) celui ci sera cherché dans son répertoire à la racine de l'application. Sinon il sera cherché dans son répertoire depuis le module demandé par la requête.


Sally
-----

**Définir un paramètre global**

    Sally::set('name', 'value');
    // il est possible d'avoir des paramètres enfants
    Sally::set('domain', 'name_b', 'value');

**Récupérer un paramètre global**

    Sally::get('name');
    // or
    Sally::get('domain'); // return array('name_b' => 'value');
    // or
    Sally::get('domain', 'name'); // return value

**Récupérer l'instance**

    $sally = Sally::getInstance();

**Appeler une librairie**

getLibrary(); s'occupe simplement de faire un "require_once" sur le fichier qui vous intéresse dans votre répertoire "libs", par exemple :

    $sally->library('Mustache/Autoloader.php');
    $sally->library('Predis/autoload.php');

Controller
----------

**__contruct**

Si vous ajoutez votre méthode __contruct au controleur il faudra faire appel manuellement au contructeur parent, sans oublier de transmettre l'objet $engine :

    class IndexController extends sally\Controller
    {
      public function __construct($engine)
      {
        parent::__construct($engine);
      }
    }

**Charger un helper**

    $this->helper->add('/toStrong');
    
**Redirection**

    $this->redirect('http://google.fr');

**Rediriger vers une autre action et/ou un autre controleur et/ou un autre model dans la même requête**

    $this->forward($action, $controleur, $module);

Il est nécessaire de préciser au moins l'action (controleur et module seront ceux en cours). Exemple :

    class IndexController extends sally\Controller
    {
      public function index()
      {
        $this->forward('maintenance', 'erreur');
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

**Désactiver la vue par defaut d'une action de controleur**

    $this->view->disableControllerView();

**Savoir si la vue par defaut été désactivé**

    $this->view->controllerViewIsEnabled(); // Boolean


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

    $db = sally\Db::getInstance();

**SGBD pris en charges**

- Mysql (avec PDO), type=mysql-pdo

**Ajouter une connexion à une base de données**

Vous pouvez en ajouter plusieurs, seul le nom doit changer. Pour ne pas avoir besoin de préciser le nom à chaque fois, utilisez le nom "default" pour la connection principale.

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

**Récupérer les valeurs des données passées dans la requête**

    $this->request->getSegment('dataName1'); // False si inexistante

**Écraser des valeurs passées dans la requête**

    $this->request->setSegment('dataName1', 'dataValue1');

**Récupérer des données $_POST**

    $this->request->getPost('name'); // value or false

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

Sally créer un cookie dont la valeur est cryptée avec l'algo Rijndael en 128b (MCRYPT_RIJNDAEL_128). La valeur correspond à un tableau sérialisé contenant vos informations.

**Récupérer l'instance**

    $session = sally\Session::getInstance();

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

Les helpers sont de basiques fonctions PHP (ou ce que vous voullez) appelable n'importe ou.

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
- layoutDelivery : Appelée avant la livraison du layout;
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

**layoutDelivery**

Avant de livrer le layout vous pourriez faire des modifications sur son contenu.

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