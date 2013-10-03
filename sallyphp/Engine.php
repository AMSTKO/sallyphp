<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Engine
*/
class Engine
{
  /**
   * @var string
  */
  private $_content = null;

  /**
   * @var mixed
  */
  private $_databack = null;

  /**
   * @var boolean
  */
  private $_forward = false;
  private $_redirect = false;

  /**
   * @var object
  */
  public $trafficker;
  public $view;
  public $layout;
  public $helper;
  public $request;

  /**
   * @param string, array
  */
  public function __construct($request_string = '', $method = null)
  {
    // first step
    $this->request = new Request($method);
    $this->request->path($request_string);

    // next
    $this->trafficker = new Trafficker($this);
    $this->view = new View($this);
    $this->layout = new Layout($this);
    $this->helper = new Helper($this);
  }

  /**
   * chargement des trafiquants , controleur, action, layout;
   * redirection interne;
   * redirection client;
   * écrire cookie;
   * @return string
  */
  public function execute()
  {
    try {
      // preDeal n'est pas de nouveau executé en cas de redirection interne 
      if (!$this->trafficker->preEngineIsExecute()) {
        $this->trafficker->preEngine();
      }

      // chemin du controleur
      if ($module = $this->request->getModule()) {
        $controller_class_name = ucfirst($module) . '_' . ucfirst($this->request->getController()) . 'Controller';
        $controller_path = \Sally::get('application') . '/modules/' . $module . '/controllers/' . $controller_class_name . '.php';
      } else {
        $controller_class_name = ucfirst($this->request->getController()) . 'Controller';
        $controller_path = \Sally::get('application') . '/controllers/' . $controller_class_name . '.php';
      }

      if (!file_exists($controller_path)) {
        throw new Exception('Le controleur "' . $this->request->getController() . '" n\'est pas accessible.');
      }

      require_once $controller_path;
      
      // demarrage du tampon de sortie
      ob_start();

      // instanciation du controleur
      $controller = new $controller_class_name($this);

      // forward demandé dans le __construct du controleur
      if ($this->_forward) {
        return $this->launchForward();
      }

      // check si l'action existe
      if (!method_exists($controller, $this->request->getAction())) {
        throw new Exception('L\'action "' . $this->request->getAction() . '" n\'existe pas dans le controller "' . $this->request->getController() . '".');
      }

      // appel de l'action du controleur
      $this->_databack = $controller->{$this->request->getAction()}();

      // en cas de redirection client
      if ($this->_redirect) {
        ob_end_clean();
      } else {
        // forward demandé dans l'action du controleur
        if ($this->_forward) {
          return $this->launchForward();
        }

        // Vue par defaut
        if ($this->view->controllerViewIsEnabled()) {
          echo $this->view->load($this->request->getController() . '/' . $this->request->getAction(), null, true);
        }

        // Fin et récupération du tampon de sortie
        $this->_content = ob_get_contents();
        ob_end_clean();

        // Place le tampon de sortie dans un layout si possible
        if ($this->layout->isDefined() && $this->layout->isEnabled()) {
          $this->_content = $this->layout->integrate($this->_content);
        }

        // Dernière action du traffiquant
        $this->_content = $this->trafficker->engineDelivery($this->_content, $this->_databack);
      }

      // Écrire du cookie
      if (class_exists('sally\Session')) {
        Session::getInstance()->sendHeaderCookie();
      }

      // Redirection client
      if ($this->_redirect) {
        header('Location: ' . $this->_redirect);
        exit;
      }

      return $this->_content;
    } catch (Exception $e) {
      exit;
    }
  }

  /**
   * Lance le forward (redirection interne);
   * Execute de nouveau $this->execute();
   * @return string
  */
  private function launchForward()
  {
    if ($this->_forward['module'] == null) {
      $this->request->setModule($this->request->getModule());
    } else {
      $this->request->setModule($this->_forward['module']);
    }

    if ($this->_forward['controller'] == null) {
      $this->request->setController($this->request->getController());
    } else {
      $this->request->setController($this->_forward['controller']);
    }

    $this->request->setAction($this->_forward['action']);
    ob_end_clean();
    $this->disableForward();
    return $this->execute();
  }

  /**
   * Définit le forward
   * @param array
  */
  public function setForward($data = array())
  {
    $this->_forward = $data;
  }

  /**
   * Désactive le forward
  */
  private function disableForward()
  {
    $this->_forward = false;
  }

  /**
   * Définit une redirection client
  */
  public function setRedirect($url)
  {
    $this->_redirect = $url;
  }

  /**
   * Détermine le chemin des fichiers helper, layout et view
   * @param string name, string file type
   * @return array path, file name
  */
  public function getFilePath($name, $type)
  {
    try {
      $module_path = '';

      if (preg_match('/\//', $name)) {
        $pre_file = substr($name, strrpos($name, '/') + 1);
        $path = substr($name, 0, strrpos($name, '/') + 1);
        if ($path == '/') {
          $path = '';
        }
      } else {
        $pre_file = $name;
        $path = '';
      }

      if ($this->request->getModule() && $name[0] != '/') {
        $module_path = 'modules/' . $this->request->getModule() . '/';
      }

      // helper
      if ($type == 'helper') {
        $directory = 'helpers';
        $file = $pre_file . 'Helper';
      } 

      // layout
      elseif ($type == 'layout') {
        $directory = 'layouts';
        $file = $pre_file . 'Layout';
      }

      // view
      elseif ($type == 'view') {
        $directory = 'views';
        $file = $pre_file . 'View';
      }

      // pas pris en charge
      else {
        throw new Exception('Le fichier "' . $name . '" ayant pour type "' . $type . '" n\'est pas pris en charge');
      }

      $path = \Sally::get('application') . '/' . $module_path . $directory . '/' . $path . $file . '.php';

      if (!file_exists($path)) {
        throw new Exception('Le fichier "' . $path . '" n\'existe pas.');
      }

      return array($path, $file);
    } catch (Exception $e) {
      exit;
    }
  }
}