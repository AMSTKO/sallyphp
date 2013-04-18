<?php

class Sally
{

  const name = 'SallyPHP MVC Framework';
  const version = '1.130309.1';
  const site = 'sallyphp.com';
  const path = __DIR__;

  private $_out = null;
  private $_cfg = array();
  private $_module = array();
  protected static $_instance = false;

  public function __construct()
  {
    header('Content-Type: text/html; charset=utf-8');
    set_exception_handler(array($this, 'exception'));

    $this->request = Sally_Request::getInstance();

    $this->_cfg['application'] = $_SERVER['DOCUMENT_ROOT'] . '/application';
    $this->_cfg['path'] = '/';
    $this->_cfg['controller.default'] = 'index';
    $this->_cfg['module.default'] = 'site';
    $this->_cfg['rijndael.key'] = 'define a key';
    $this->_cfg['cookie.domain'] = null;
    $this->_cfg['cookie.name'] = 'sally';
  }

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function init($request_string = '')
  {
    $pre_request = explode('/', substr($request_string, strrpos(Sally::get('path'), '/')));
    $hasModule = count($this->_module) > 0 ? true : false;
    $passe = false;
    $requestIndex = 0;
    $module_name = null;
    $controller_name = null;
    $action_name = null;
    $logicControllerIndex = 0;
    $logicActionIndex = 1;
    $logicDataIndex = 2;

    foreach ($pre_request as $key => $row) {
      $row = strtolower($row);
      if ($passe) {
        $passe = false;
        continue;
      }
      if (!empty($row)) {
        if ($requestIndex >= $logicDataIndex) {
          $value = $pre_request[($key + 1)];
          if (!empty($value)) {
            $this->request->data[$row] = $value;
            $passe = true;
          }
        } else {
          if ($hasModule) {
            if ($requestIndex == 0) {
              if (in_array($row, $this->_module)) {
                $module_name = $row;
                $logicControllerIndex++;
                $logicActionIndex++;
                $logicDataIndex++;
              }
            }
          }
          if ($requestIndex == $logicControllerIndex) {
            $controller_name = $row;
          }
          if ($requestIndex == $logicActionIndex) {
            $action_name = $row;
          }
        }
        $requestIndex++;
      }
    }

    if ($hasModule) {
      if ($module_name != null) {
        $this->request->module = $module_name;
      } else {
        $this->request->module = strtolower($this->get('module.default'));
      }
    }

    if ($controller_name != null) {
      $this->request->controller = $controller_name;
    } else {
      $this->request->controller = strtolower($this->get('controller.default'));
    }

    if ($action_name != null) {
      $this->request->action = $action_name;
    } else {
      $this->request->action = 'index';
    }

    return $this->call();
  }

  public function getFile($value, $type)
  {
    $module = '';

    if (preg_match('/\//', $value)) {
      $pre_file = strtolower(substr($value, strrpos($value, '/') + 1));
      $path = substr($value, 0, strrpos($value, '/') + 1);
    } else {
      $pre_file = $value;
      $path = '';
    }

    if ($this->request->module && $value[0] != '/') {
      $module = 'modules/' . $this->request->module . '/';
    }

    if ($type == 'helper') {
      $directory = 'helpers';
      $file = $pre_file . 'Helper';
    } elseif ($type == 'layout') {
      $directory = 'layouts';
      $file = $pre_file . 'Layout';
    } elseif ($type == 'controller') {
      $directory = 'controllers';
      $file = ucfirst($pre_file) . 'Controller';
    } elseif ($type == 'model') {
      $directory = 'models';
      $file = ucfirst($pre_file) . 'Model';
    } elseif ($type == 'trafficker') {
      $directory = 'traffickers';
      $file = ucfirst($pre_file) . 'Trafficker';
      $module = '';
    } elseif ($type == 'view') {
      $directory = 'views';
      $file = $pre_file . 'View';
    } else {
      throw new Exception('getFile type error');
    }

    $path = Sally::get('application') . '/' . $module . $directory . '/' . $path . $file . '.php';

    if (!file_exists($path)) {
      throw new Exception('Le fichier "' . $path . '" n\'existe pas.');
    }

    return array($path, $file);
  }

  private function call()
  {
    $trafficker = Sally_Trafficker::getInstance();
    $trafficker->preDeal();

    list($controller_file, $controller_class_name) = $this->getFile($this->request->controller, 'controller');

    ob_start();
    require $controller_file;
    $controller = new $controller_class_name();

    if (!method_exists($controller, $this->request->action)) {
      throw new Exception('L\'action "' . $this->request->action . '" n\'existe pas dans le controller "' . $this->request->controller . '".');
    }

    if (method_exists($controller, 'init')) {
      $controller->init();
    }

    $controller->{$this->request->action}();

    $view = Sally_View::getInstance();
    if ($view->_display) {
      $view->load($this->request->controller . '/' . $this->request->action);
    }

    $this->_out = ob_get_contents();
    ob_end_clean();

    $layout = Sally_Layout::getInstance();
    if ($layout->use) {
      $this->_out = $layout->integrate($this->_out);
    }

    $trafficker->preDelivery();
    return $this->_out;
  }

  public function addModule($name)
  {
    $module_name = strtolower($name);
    if (!in_array($module_name, $this->_module)) {
      if (!is_dir(Sally::get('application') . '/modules/' . $module_name)) {
        throw new Exception('Le module "' . Sally::get('application') . '/modules/' . $module_name .'" n\'existe pas.');
      }
      array_push($this->_module, $module_name);
    }
  }

  public static function get($name)
  {
    $instance = self::getInstance();
    if (array_key_exists($name, $instance->_cfg)) {
      return $instance->_cfg[$name];
    } else {
      throw new Exception('Le paramètre de configuration "' . $name .'" n\'existe pas.');
    }
  }

  public static function set($name, $value)
  {
    $instance = self::getInstance();
    $instance->_cfg[$name] = $value;
    return true;
  }

  public function exception($e)
  {
    $tmp = '';
    $tmp.= '<style>#spException{font-family: Verdana; font-size: 14px}#spException h2{color: #212121}#spException p{padding-left: 20px}</style>';
    $tmp.= '<div id="spException">';
      $tmp.= '<h2>#bad trip</h2>';
      $tmp.= '<p>';
        $tmp.= '<b>' . $e->getMessage() . '</b><br>'; 
        $tmp.= '<i>at line </i><b>' . $e->getLine() . '</b>';
        $tmp.= '<i> in file </i><b>' . $e->getFile() . '</b><br><br>';
        foreach ($e->getTrace() as $id => $trace) {
          $tmp.= '<b>' . $id . '</b> ' . $trace['file'] . ' line ' . $trace['line'] . '<br>';
        }
        $tmp.= '<br>';
        $tmp.= '<small>' . Sally::name . ' ' . Sally::version . '</small>';
      $tmp.= '<p>';
    $tmp.= '</div>';
    echo $tmp;
    exit;
  }
}