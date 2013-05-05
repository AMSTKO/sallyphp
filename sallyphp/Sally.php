<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Sally
{
  const name = 'SallyPHP MVC Framework';
  const version = '1.130405.1';
  const site = 'sallyphp.com';
  const path = __DIR__;
  private $_out = null;
  private $_dataBack = null;
  private $_forward = false;
  private $_cfg = array();
  private $_module = array();
  protected static $_instance = false;

  public function __construct()
  {
    header('Content-Type: text/html; charset=utf-8');
    set_exception_handler(array($this, 'exception'));
    spl_autoload_register(array($this, 'loader'));

    $this->request = Request::getInstance();

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
            $this->request->setSegment($row, $value);
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
        $this->request->setModule($module_name);
      } else {
        $this->request->setModule(strtolower($this->get('module.default')));
      }
    }

    if ($controller_name != null) {
      $this->request->setController($controller_name);
    } else {
      $this->request->setController(strtolower($this->get('controller.default')));

    }

    if ($action_name != null) {
      $this->request->setAction($action_name);
    } else {
      $this->request->setAction('index');
    }

    return $this->call();
  }

  public function loader($class)
  {
    $segments = preg_split('/(?=[A-Z_])/', $class, 0, PREG_SPLIT_NO_EMPTY);
    $what = end($segments);
    $segmentsNb = count($segments);

    if ($what == 'Model' && $segmentsNb > 1) {
      if (isset($segments[1]) && $segments[1] == '_') {
        $path = Sally::get('application') . '/modules/' . strtolower($segments[0]) . '/models/' . $class . '.php';
      } else {
        $path = Sally::get('application') . '/models/' . $class . '.php';
      }
    } elseif ($what == 'Controller' && $segmentsNb > 1) {
      if ($module = $this->request->getModule()) {
        $path = Sally::get('application') . '/modules/' . $module . '/controllers/' . $class . '.php';
      } else {
        $path = Sally::get('application') . '/controllers/' . $class . '.php';
      }
    } elseif ($what == 'Trafficker' && $segmentsNb > 1) {
      $path = Sally::get('application') . '/traffickers/' . $class . '.php';
    } else {
      $path = Sally::path . '/' . $class . '.php';
    }

    if (!file_exists($path)) {
      throw new Exception('Le fichier "' . $path . '" n\'existe pas.');
    }

    include $path;
  }

  public function getFile($value, $type)
  {
    $module = '';

    if (preg_match('/\//', $value)) {
      $pre_file = strtolower(substr($value, strrpos($value, '/') + 1));
      $path = substr($value, 0, strrpos($value, '/') + 1);
      if ($path == '/') {
        $path = '';
      }
    } else {
      $pre_file = $value;
      $path = '';
    }

    if ($this->request->getModule() && $value[0] != '/') {
      $module = 'modules/' . $this->request->getModule() . '/';
    }

    if ($type == 'helper') {
      $directory = 'helpers';
      $file = $pre_file . 'Helper';
    } elseif ($type == 'layout') {
      $directory = 'layouts';
      $file = $pre_file . 'Layout';
    } elseif ($type == 'view') {
      $directory = 'views';
      $file = $pre_file . 'View';
    } else {
      throw new Exception('getFile type error, ' . $type);
    }

    $path = Sally::get('application') . '/' . $module . $directory . '/' . $path . $file . '.php';

    if (!file_exists($path)) {
      throw new Exception('Le fichier "' . $path . '" n\'existe pas.');
    }

    return array($path, $file);
  }

  private function call()
  {
    $trafficker = Trafficker::getInstance();

    if (!$trafficker->preDealIsExec()) {
      $trafficker->preDeal();
    }

    $controller_class_name = ucfirst($this->request->getController()) . 'Controller';

    ob_start();
    $controller = new $controller_class_name();

    if (!method_exists($controller, $this->request->getAction())) {
      throw new Exception('L\'action "' . $this->request->getAction() . '" n\'existe pas dans le controller "' . $this->request->getController() . '".');
    }

    $this->_dataBack = $controller->{$this->request->getAction()}();
    if ($this->_forward) {
      ob_end_clean();
      $this->disableForward();
      return $this->call();
    }

    $view = View::getInstance();
    if ($view->controllerViewIsEnabled()) {
      echo $view->load($this->request->getController() . '/' . $this->request->getAction(), null, true);
    }

    $this->_out = ob_get_contents();
    ob_end_clean();

    $layout = Layout::getInstance();
    if ($layout->isDefined() && $layout->isEnabled()) {
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

  public function getDataBack()
  {
    return $this->_dataBack;
  }

  public function getOut()
  {
    return $this->_out;
  }

  public function setOut($out)
  {
    $this->_out = $out;
  }

  public static function get($name)
  {
    $instance = self::getInstance();
    if (array_key_exists($name, $instance->_cfg)) {
      return $instance->_cfg[$name];
    } else {
      return false;
    }
  }

  public static function set($name, $value)
  {
    $instance = self::getInstance();
    $instance->_cfg[$name] = $value;
    return true;
  }

  public function enableForward()
  {
    $this->_forward = true;
  }

  public function disableForward()
  {
    $this->_forward = false;
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