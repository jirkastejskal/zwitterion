<?php
class ZwController
{
    /**
     *  @var ZwApplication
     */
    public $theApp;

    /**
     *  @var string
     */
    protected $viewScriptName = "";

    /**
     *  @var string
     */
    protected $layoutScriptName = "layout";

    /**
     *  @var array|null
     */
    protected $params = null;

    /**
     *  @var string
     */
    public $title = "";

    /**
     * @param ZwApplication $app
     * @param array $params
     */
    public function __construct($app,$params)
    {
        $this->theApp = $app;
        $this->viewScriptName = $params['action'];
        $this->params = $params;
    }

    public function init()
    {
    }

    public function render()
    {
        if ($this->layoutScriptName != "") {
            include $this->theApp->sitePath.'/'.$this->layoutScriptName.'.phtml';
        } else $this->viewScript();
    }
    private function viewScript()
    {
        if ($this->viewScriptName != "") {
            include $this->theApp->sitePath.'/'.$this->params['controller'].'/views/'.$this->viewScriptName.'.phtml';
        }
    }

    /**
     * @param string $url
     */
    protected function _redirect($url)
    {
        header('Location: '.$url,true,302);
        die();
    }

    /**
     * @return ZwDatabase
     */
    protected function db()
    {
        return $this->theApp->db();
    }

}