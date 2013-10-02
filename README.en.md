SallyPHP
========

SallyPHP is a lightweight and easy-to-use HMVC PHP framework for fast & robust web applications. It provides simple tools for structured and rich applications from small to large scale.
Check out the source, it's like an advertisement!

## Hello world!

Caracteristics of the example given in the application directory:

- HMVC Structure (the modules directory contains multiple MVC substructures)
- Ready to use in a local environment (http://127.0.0.1/sallyphp/index.php/index)

Apache configuration :

    Alias /sallyphp "/var/www/sallyphp/public"
    <Directory "/var/www/sallyphp/public">
        SetEnv environnement local
    </Directory>

Summary
--------

- [Structure](#structure)
- [Inventory](#inventory)
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
    sallyphp/ (classes of Sally)


Inventory
----------

These are the classes you might need during the develement.

    $sally = Sally::getInstance();
    $acl = Acl::getInstance();
    $db = Db::getInstance();
    $request = Request::getInstance();
    $layout = Layout::getInstance();
    $helper = Helper::getInstance();
    $session = Session::getInstance();
    $trafficker = Trafficker::getInstance();

**Miscellaneous**

    $rijndael = Rijndael::getInstance();
    $PHPMailer = PHPMailer::getInstance();

Notes
-----

**slash before the element to load**

The thing you'll want to load (helper, view, layout) will be searched in the module directory related to the query.
However, if you want to load it by searching from the root of your application, add a slash before its name.


Sally
-----

**Define global parameters**

    Sally::set('name', 'value');
    // It is possible to have children datasets
    Sally::set('domain', 'name_b', 'value');

**Get global parameters**

    Sally::get('name');
    // or
    Sally::get('domain'); // return array('name_b' => 'value');
    // or
    Sally::get('domain', 'name'); // return value

**Get the Sally instance**

    $sally = Sally::getInstance();

**Get or remplace output**

*Get the content to be sent to the browser:*

    $sally->getOut();

*Replace the output to send something else:*

    $sally->setOut('your content...');

Use case: In a *trafficker*, before sending the content to the browser, you can remove all spaces and tabs to save bandwidth.

    class MyTrafficker extends TraffickerAbstract
    {
      function preDelivery()
      {
        $sally->setOut(preg_replace('/[\s\t]+/', ' ', $sally->getOut());
      }
    }

**Get controller output**

Controllers can return data. You can get these in any **trafficker** to modify it (the example of adding a token is in the trafficker section).

    $sally->getDataBack();

**Load a library**

getLibrary does a simple "require_once" on a file in the "libs" directory.

    $sally->getLibrary('Mustache/Autoloader.php');

Controller
----------

In a controller, you easily have access to the Request, View and Layout classes through $this->...

**__contruct**

You can add your own __contruct method to the controller by calling the parent's constructor:

    class IndexController extends Controller
    {
      public function __construct()
      {
        parent::__construct();
      }
    }

**Send variables to the view**

    $this->view->setData('name1', 'value1');

    // or

    $this->view->setData(array(
      'name1' => 'value1',
      'name2' => 'value2'
    ));

    // in view file : echo $name1; // value1

**Change view**

    echo $this->view->load('/sidebar', array(
      'login' => 'Mr.Ping'
    ));

    // in view file : echo $login; // Mr.Ping

**Load a helper**

    $this->helper('/tostrong');
    
**Redirections**

    $this->redirect('http://google.fr');

**Redirect to another action**

    $this->forward($action, $controller, $module);

It is only required to provide at least the name of the action (the current controller and module will be used). For example :

    class IndexController extends Controller
    {
      public function index()
      {
        $this->forward('maintenance', 'error');
      }
    }

The index page will now redirect transparently to the "maintenance" action from the controller "error".

View
----

**Get the View instance**

    $view = View::getInstance();

**Disable autoloading of the view linked to the action of the controller**

    $view->disableControllerView();

**Check if autoload is enable**

    $view->controllerViewIsEnabled(); // Boolean


Layout
------

**Get the Layout instance**

    $layout = Layout::getInstance();

**Set a layout to your page**

    $layout->set('/home');

**Disabled the layout**

    $layout->disableLayout();

**Check if the layout is enabled**

    $layout->isEnabled(); // Boolean

**Check if a layout is defined**

    $layout->isDefined(); // Boolean

**Send variables to the layout**

    $layout->setData('name1', 'value1');

    // or

    $layout->setData(array(
      'name1' => 'value1',
      'name2' => 'value2'
    ));

    // in view file : echo $name1; // value1

**Get variables sent to the layout**

    $layout->getData('name1'); // return value1


Acl
---

**Get the Acl instance**

    $acl = Acl::getInstance();

**Add roles**

    $acl->addRole('guest');
    $acl->addRole('user', 'guest');

**Add resources**

    $acl->AddRessource('public');
    $acl->AddRessource('account');

**Add rights**

    $acl->allow('guest', 'public');
    $acl->allow('guest', 'account', array('signin', 'signup', 'request'));
    $acl->allow('user', 'account');

**Add restrictions**

    $acl->deny('guest', 'public', array('action_name'));

**Check if a user is allowed to access to a specific resource**

    if (!$acl->isAllowed($role_name, $ressource_name, $action_name)) {
      exit;
    }


Db
--

**Get the Db instance**

    $db = Db::getInstance();

**Databases supported**

- Mysql (with PDO)

**Add a database**

    $db->add(array(
      'type' => 'mysql_pdo',
      'host' => '127.0.0.1',
      'dbname' => 'db_name',
      'user' => 'db_user',
      'passwd' => 'db_pasword'
    ));

**Add other databases**

DbSally Lets you have multiple databases. You only need to add a name to your database settings. By default, the name is *default*.

    $db->add(array(
      'name' => 'main'
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

**Get the database instance**

Without arguments, the default database will be returned.

    $db = Db::getConnection();

Otherwise you only need to name the database.

    $db = Db::getConnection('other');

**Example with PDO**

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

The allowed forms of requests are :

- /module/controller/action
- /controller/action (by defining a default module in the configuration)
- /controller/action/dataName1/dataValue1/dataName2/dataValue2

**Get the Request instance**

    $request = Request::getInstance();

**Get data from the request**

    $request->getSegment('dataName1'); // False if not found

**Remplace values from the request**

    $request->setSegment('dataName1', 'dataValue1');

**Get data from $_POST**

    $request->getPost('name'); // False if not found

**Change the module requested**

    $request->setModule('module_name');

**Change the controller requested**

    $request->setController('controller_name');

**Change the action requested**

    $request->setAction('action_name');

**Get the module requested**

    $request->getModule();

**Get the controller requested**

    $request->getController();

**Get the action requested**

    $request->getAction();


Session
-------

The cookie created by Sally contains an encrypted (using Rijndael 128b MCRYPT_RIJNDAEL_128) serialized table containing your data.

**Get the Session instance**

    $session = Session::getInstance();

**Check if the user already has the cookie**

    $session->hasCookie(); // Boolean

**Add data to the cookie**

    $session->set('logged', 1);

**Get a specific value in the cookie**

    $session->get('logged');

**Get the entire data table in the cookie**

    $session->getContent();

**Replace the content of the cookie**

    $session->setContent();

    // or

    $session->setContent(array(
      'logged' => 1,
      'username' => 'Pingoo'
    ));


Helper
------

Helpers are basic PHP functions you can access from anywhere.

**Get instance**

    $helper = Helper::getInstance();

**Load a helper**

    $helper->load('helper_name');

**Example of helper : toStrongHelper.php**
    
    <?php
    function toStrong($text)
    {
      echo '<strong>' . $text . '</strong>';
    }


Trafficker
----------

A trafficker lets you act at 4 moments using one class:

- before calling a controller;
- before the rendering of a layout;
- before the rendering of a view;
- before returning the content of the response;

**Before calling a controller : preDeal() {}**

Intercept the request before processing. With a Request or Layout instance you can:

- Redefine the module, controller or action name;
- Check ACL rights and change the controller or action if needed;
- Define a layout depending on the user;
- Show an error;
- ...

**Before the rendering of a layout : preLayout() {}**

For example, define main variables that every layout should have by using $this->layout->setData()

**Before the rendering of a view : preView() {}**

If you have a template engine:

    function preView($out, $data) // $out: view content, $data: array
    {
        $m = new Mustache_Engine;
        return $m->render($out, $data);
    }

**before returning the content of the response : preDelivery() {}**

Modify the response at the very last time.

- add a token;
- add data (processing time...);
- ...

**Get the Trafficker instance**

    $trafficker = Trafficker::getInstance();

**Add a trafficker**

    $trafficker->add('my');

**Example of trafficker : MyTrafficker.php**

You'll have a huge number of AJAX requests. So every controller should have an action "request" that processes these and return for example JSON data. Firstly, with preDeal disable the layout and the view. Then with preDelivery, add data(a token in this case) to the generated response.

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

**Get the Rijndael instance**

    $rijndael = Rijndael::getInstance();

**Define a key**

    $rijndael->setKey('your key');

**Encrypt data**

    $rijndael->encrypt('data');

**Decrypt data**

    $rijndael->decrypt('dataCrypted');


PHPMailer
---------

For more info, go to https://github.com/Synchro/PHPMailer

**Get the PHPMailer instance**

    $PHPMailer = PHPMailer::getInstance();

**Settings with Mailjet for example**

    $PHPMailer->IsSMTP();
    $PHPMailer->Host = 'in.mailjet.com';
    $PHPMailer->Port = 587;
    $PHPMailer->SMTPAuth = true;
    $PHPMailer->Username = 'username';
    $PHPMailer->Password = 'password';
    $PHPMailer->SMTPSecure = 'tls';

**Send an email**

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