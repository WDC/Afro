<?php 

	/**
	 * Afro - The simplistic PHP port of Sinatra for Ruby.
	 * 
	 * @author  James <ukjbrooks@gmail.com>
	 * @version 1.0
	 */

	define('AJAX', 'XMLHttpRequest');

	function get($route, $callback) {
		Afro::process($route, $callback, 'GET');
	}

	function post($route, $callback) {
		Afro::process($route, $callback, 'POST');
	}

	function put($route, $callback) {
		Afro::process($route, $callback, 'PUT');
	}

	function delete($route, $callback) {
		Afro::process($route, $callback, 'DELETE');
	}

	function ajax($route, $callback) {
		Afro::process($route, $callback, AJAX);
	}

	class Afro {
		public static $foundRoute = FALSE;

		public $URI = '';
		public $params = array();
		public $method = '';
		public $format = '';
		public $paramCount = 0;
		public $payload = array();

		public static function getInstance() {
			static $instance = NULL;
			if($instance === NULL) $instance = new Afro;
			return $instance;
		}

		public static function run() {
			if(!static::$foundRoute) {
				trigger_error('The requested route is not defined!');
			}

			ob_end_flush();
		}

		public static function process($route, $callback, $type) {
			$Afro = static::getInstance();

			if($type === AJAX)
				$Afro->method = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : 'GET';

			if(static::$foundRoute || (!preg_match('@^'.$route.'(?:\.(\w+))?$@uD', $Afro->URI, $matches) || $Afro->method != $type))
				return FALSE;

			$lastExt = $matches[count($matches) - 1];

			$Afro->format = pathinfo($lastExt, PATHINFO_EXTENSION);

			static::$foundRoute = TRUE;
			return $callback($Afro);
		}

		public function __construct() {
			ob_start();
			$this->URI = $this->getURI();
			$this->params = explode('/', trim($this->URI, '/'));
			$this->paramCount = count($this->params);
			$this->method = $this->getMethod();
			$this->payload = $GLOBALS['_' . $this->method];
		}

		public function param($num) {
			$num--;
			$this->params[$num] = isset($this->params[$num]) ? basename($this->params[$num], '.' . $this->format) : NULL;
			return isset($this->params[$num]) ? $this->params[$num] : NULL;
		}

		protected function getMethod() {
			return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		}

		protected function getURI($prefixSlash = TRUE) {
			if(isset($_SERVER['PATH_INFO'])) {
				$uri = $_SERVER['PATH_INFO'];
			}elseif(isset($_SERVER['REQUEST_URI'])) {
				$uri = $_SERVER['REQUEST_URI'];
				
				if(strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
					$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
				}elseif(strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
					$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
				}

				if(strncmp($uri, '?/', 2) === 0) $uri = substr($uri, 2);

				$parts = preg_split('#\?#i', $uri, 2);
				$uri = $parts[0];
				
				if(isset($parts[1])) {
					$_SERVER['QUERY_STRING'] = $parts[1];
					parse_str($_SERVER['QUERY_STRING'], $_GET);
				}else {
					$_SERVER['QUERY_STRING'] = '';
					$_GET = array();
				}
				$uri = parse_url($uri, PHP_URL_PATH);
			}else {
				return FALSE;
			}

			return ($prefixSlash ? '/' : '') . str_replace(array('//', '../'), '/', trim($uri, '/'));
		}

		public function format($name, $callback) {
			$Afro = static::getInstance();

			if(!empty($Afro->format) && $name == $Afro->format)
				return $callback($Afro);
			else return FALSE;
		}
		
		public function response($data, $for = NULL, $echo = TRUE) {
			$Afro = static::getInstance();
			if (is_null($for) && !empty($Afro->format)) {
				$for = $Afro->format;
			}
			$for = strtolower($for);
			switch ($for) {
				case 'json':
					if ($echo) {
						echo json_encode($data);
					}
					else {
						return json_encode($data);
					}
					break;
				case 'csv':
					$string = '';
					foreach ($data as $line) {
						$string .= implode(',', $line) . "\n";
					}
					if ($echo) {
						echo $string;
					}
					else {
						return $string;
					}
					break;
			
			}
		}

	}

	$Afro = Afro::getInstance();

?>
