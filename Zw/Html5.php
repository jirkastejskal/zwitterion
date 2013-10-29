<?php
class ZwHtml5
{
    /**
     * @param ZwController $ctrl
     */
    static public function renderHeader($ctrl)
    {
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        $vars = $ctrl->theApp->ini['head'];
        echo "<!DOCTYPE html>\n";
//        echo "<!--[if lt IE 7 ]> <html lang=\"".$vars['lang']."\" class=\"ie6 ielt8\"> <![endif]-->\n";
//        echo "<!--[if IE 7 ]>    <html lang=\"".$vars['lang']."\" class=\"ie7 ielt8\"> <![endif]-->\n";
//        echo "<!--[if IE 8 ]>    <html lang=\"".$vars['lang']."\" class=\"ie8\"> <![endif]-->\n";
//        echo "<!--[if (gte IE 9)|!(IE)]><!--> <html lang=\"".$vars['lang']."\"> <!--<![endif]-->\n";
        echo "<head>\n";
        echo "<meta charset=\"".$vars['charset']."\">\n";

        $local = $ctrl->theApp->production != true;
        if (isset($vars['closure'])) {
            if ($local && !isset($vars['closure_release'])) {
                echo "  <script type=\"text/javascript\">\n    var CLOSURE_NO_DEPS = true;\n";
                echo "    var CLOSURE_BASE_PATH = \"http://127.0.0.2\";\n";
                echo "  </script>\n";
                $vars['javascripts'].=';http://127.0.0.2/closure/closure/goog/base.js;http://127.0.0.2/promyka/deps.js';
            } else {
                $vars['javascripts'].=';/frontend/'.$vars['closure'].'.js.gz';
            }
        }
        echo "<meta name=\"keywords\" content=\"".$vars["keywords"]."\" />\n";
        echo "<meta name=\"author\" content=\"".$vars["site"]." - ".$vars["email"]."\"/>\n";
        echo "<meta name=\"robots\" content=\"index, follow\" />\n";
        echo "<meta name=\"description\" content=\"".$vars["site"]."\" />\n";
        if (isset($vars['favicon']))
            echo "<link rel=\"shortcut icon\" href=\"".$vars['favicon']."\" />\n";
        else
            echo "<link rel=\"shortcut icon\" href=\"/favicon.ico\" />\n";
        $title = sprintf($vars['title'],$ctrl->title);
        echo "<title>".$title."</title>\n";
        $elems = explode(';',$vars['stylesheets']);
        foreach($elems as $elem) {
            if ($elem == "")
                continue;
            echo "<link rel=\"stylesheet\" href=\"$elem".self::md5offile($elem)."\" />\n";
        }
        $elems = explode(';',$vars['javascripts']);
        foreach($elems as $elem) {
            if ($elem == "")
                continue;
            echo "<script src=\"$elem".self::md5offile($elem)."\" ></script>\n";
        }
        if (isset($vars['closure']) && $local && !isset($vars['closure_release'])) {
            echo "<script type=\"text/javascript\">\n";
            echo "    goog.require('".$vars['closure']."');\n";
            echo "</script>\n";
        }
        echo "</head>\n";
    }
    static private function md5offile($fl)
    {
        if (strpos($fl,'?') !== false)
            return '';
        $fl = realpath($_SERVER['DOCUMENT_ROOT'].$fl);
        return '?md5='.md5(filemtime($fl));
    }
//http://googlecode.blogspot.com/2009/12/google-analytics-launches-asynchronous.html?utm_source=feedburner&utm_medium=feed&utm_campaign=Feed%3A+blogspot%2FDcni+%28Google+Code+Blog%29&utm_content=Google+Reader
    /**
     * @param ZwController $ctrl
     */
    public static function renderFooter($ctrl)
    {
		if ($ctrl->theApp->production === true) {
            $code = $ctrl->theApp->ini['footer']['google'];
			echo "<script type=\"text/javascript\">\n";
			echo "var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
			echo "document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
			echo "</script>\n";
			echo "<script type=\"text/javascript\">\n";
			echo "try {\n";
			echo "var pageTracker = _gat._getTracker(\"$code\");\n";
			echo "pageTracker._trackPageview();\n";
			echo "} catch(err) {}</script>\n";
		}
        echo '</body></html>';
    }
}
