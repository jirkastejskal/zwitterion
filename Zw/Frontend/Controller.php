<?php
class ZwFrontendController extends ZwController
{
    public function init()
    {
        $this->layoutScriptName = '';
        $this->viewScriptName = '';
    }
    public function indexAction()
    {
        header("Cache-Control: max-age=28800");
        $file = $this->params['file'];

        $arr = explode('.',$file);
        $ext = $arr[count($arr)-1];

        if ($ext == 'gz') {
            header('Content-Encoding: gzip');
            $ext = $arr[count($arr)-2];
        }
        switch ($ext) {
            case 'css':header("Content-type: text/css");break;
            case 'jpg':header("Content-type: image/jpeg");break;
            case 'gif':header("Content-type: image/gif");break;
            case 'png':header("Content-type: image/png");break;
            case 'ico':header("Content-type: image/vnd.microsoft.icon");break;
            case 'js':header("Content-type: text/js");break;
        }
        readfile($this->theApp->sitePath.'/Frontend/'.$file);
    }

}