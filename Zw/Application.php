<?php
class ZwApplication
{
    /**
     *  @var ZwApplication
     */
    private static $_instance = null;
    /**
     * @return ZwApplication
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *  @var string
     */
    private $_env;

    /**
     * @var bool
     */
    public $production = true;
    /**
     *  @return string
     */
    public function environment()
    {
        return $this->_env;
    }

    /**
     *  @var string
     */
    public $sitePath;

    /**
     *  @var array
     */
    public $ini;

    public function __construct()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     *  @var string|null
     */
    public $logger = null;

    /**
     * @param string $name
     * @param string $key
     * @return null
     */
    public function log($name,$key='')
    {
        if ($this->logger != null) {
            $fn = $this->logger;
            $fn($name,$key);
        }
    }

    /**
     * @param string $class
     * @return string
     */
    public static function autoload($class)
    {
        if (class_exists($class, false))
            return $class;

        $pieces = array_filter(preg_split('/(?=[A-Z])/',$class),'strlen');
        require_once implode('/',$pieces).'.php';

        return $class;
    }

    public function init($site,$env)
    {
        $this->_env = $env;
        $this->production = $env == "production";

        $path = realpath(dirname(__FILE__).'/../../');
        $this->sitePath = $path.'/'.$site;
       	$paths = array(
       	    $path.'/zwitterion',
       	    $this->sitePath,
       	    get_include_path()
       	);
        set_include_path(implode(PATH_SEPARATOR, $paths));
        $this->ini = parse_ini_file($this->sitePath.'/config.ini',true);
//        echo '<pre>';
//        print_r($arr);
//        echo '</pre>';
    }
    public function dispatch()
    {
        $url = $_SERVER['REQUEST_URI'];
        $qpos = strpos($url,'?');
        $quest = "";
        if ($qpos !== false) {
            $quest = substr($url,$qpos);
            $url = substr($url,0,$qpos);
        }
        $parts = array_values(array_filter(explode('/',$url),'strlen'));
        $countParts = count($parts);
//        print_r($parts);

        $params = array();
        $params['controller'] = 'Index';
        $params['action'] = 'index';
        $found = false;
        if ($countParts > 0) {
            $paramStart = 1;
            foreach($this->ini['routing'] as $route => $target) {
                $rtpcs = explode('/',$route);
                $countPieces = count($rtpcs);
                for ($i = 0;$i<$countPieces;++$i) {
                    $piece = $rtpcs[$i];
                    if ($piece[0] == ':')
                        continue;
                    if ($i > $countParts)
                        break;
                    if ($piece != $parts[$i])
                        break;
                }
                if ($i != $countPieces)
                    continue;
                $found = true;
                for ($i = 0;$i<$countPieces && $i<$countParts;++$i) {
                    $piece = $rtpcs[$i];
                    if ($piece[0] != ':')
                        continue;
                    $paramkey = substr($piece,1);
                    $params[$paramkey] = $parts[$i];
                }
                $targetPieces = explode('/',$target);
                $params['controller'] = ucfirst($targetPieces[0]);
                if ($targetPieces[1] != ':action')
                    $params['action'] = $targetPieces[1];
                $paramStart = $countPieces;
            }
            if ($found == false) {
                if ($countParts % 2 == 1) {
                    $params['action'] = $parts[0];
                } else {
                    $params['controller'] = ucfirst($parts[0]);
                    $params['action'] = $parts[1];
                    $paramStart = 2;
                }
            }
            if (($countParts-$paramStart) % 2 == 0) {
                for ($i = $paramStart;$i<count($parts);++$i) {
                    $params[$parts[$i]] = $parts[$i+1];
                    ++$i;
                }
            }
        }

        if ($params['controller'] == 'Index' && $params['action'] == 'favicon.ico' && $found == false){
            $params['controller'] = 'Frontend';
        }
        //special default handling of frontend space
        if ($params['controller'] == 'Frontend' && $found == false) {
            $params['controller'] = 'ZwFrontend';
            $params['file'] = $params['action'];
            $params['action'] = 'index';
        }

        $this->log('Routed: '.$params['controller'].'/'.$params['action']);

        $ctrN = $params['controller'].'Controller';
        $ignored = array('-','.',',');
        $actionN = str_replace($ignored,'',$params['action']);

        $ctr = new $ctrN($this,$params);
        $ctr->init();
        if ($this->production==false && method_exists($ctr,$actionN)==false) {
            $actionN = str_replace($ignored,' ',$params['action']);
            $actionN = ucwords($actionN);
            $actionN = str_replace(' ','',$actionN);
            $actionN = lcfirst($actionN);
        }
        $actionN = $actionN.'Action';
        $ctr->$actionN();
        $ctr->render();
    }

    /**
     * @var null|ZwSession
     */
    private $_session = null;

    /**
     * @return ZwSession
     */
    public function session()
    {
        if($this->_session === null) {
            $this->_session = new ZwSession();
        }
        return $this->_session;
    }
    private $_database = null;
    /**
     * @return ZwDatabase
     */
    public function db()
    {
        if ($this->_database === null) {
            $this->_database = new ZwDatabase($this);
        }
        return $this->_database;
    }

}