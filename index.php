<?php	

	global $url;

	$url = $_GET['url'];

	define('DEVELOPMENT_ENVIRONMENT',true);
	define('ROOT', '/var/www/todo/');

	setReporting();
	removeMagicQuotes();
	unregisterGlobals();
	callHook();

	/** Main Call Function **/
	function callHook() {
		global $url;

		$urlArray = array();
		$urlArray = explode("/",$url);

		$controller = $urlArray[0];
		array_shift($urlArray);
		$action = $urlArray[0];
		array_shift($urlArray);
		$queryString = $urlArray;

		$controllerName = $controller;
		$controller = ucwords($controller);
		$model = rtrim($controller, 's');
		
		$dispatch = new $controller($controllerName);

		if ((int)method_exists($controller, $action)) {
			call_user_func_array(array($dispatch,$action),$queryString);
		} else {
			die("Method: $controller, Action:$action does not exist");
		}
	}

	/** Autoload any classes that are required **/

	function __autoload($className) {
		require_once("controller/rest.php");
	}

	/** Check if environment is development and display errors **/

	function setReporting() {
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
	}
	}

	/** Check for Magic Quotes and remove them **/

	function stripSlashesDeep($value) {
		$value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
		return $value;
	}

	function removeMagicQuotes() {
	if ( get_magic_quotes_gpc() ) {
		$_GET    = stripSlashesDeep($_GET   );
		$_POST   = stripSlashesDeep($_POST  );
		$_COOKIE = stripSlashesDeep($_COOKIE);
	}
	}

	/** Check register globals and remove them **/

	function unregisterGlobals() {
		if (ini_get('register_globals')) {
			$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
			foreach ($array as $value) {
				foreach ($GLOBALS[$value] as $key => $var) {
					if ($var === $GLOBALS[$key]) {
						unset($GLOBALS[$key]);
					}
				}
			}
		}
	}
		
	
?>