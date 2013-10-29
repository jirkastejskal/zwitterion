<?php

class PromykaEmail
{

    static public function send($tos,$subject,$html_inner)
    {
        ZwApplication::getInstance()->log('PromykaEmail::send - NOT IMPLEMENTED');
        return;
        $html  =
            '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs">'.
                '<head>'.
                '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'.
                '<meta http-equiv="content-language" content="cs"/>'.
                '</head><body>';
        $html.= $html_inner;
        $html.='</body>';

        $config = array('auth' => 'login',
            'port' => '465',
            'ssl' => 'ssl',
            'username' => 'robot@promyka.cz',
            'password' => 'robutek');
        $tr = new Zend_Mail_Transport_Smtp('smtp.gmail.com',$config);

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText('email body text');

        $mail->setFrom('robot@promyka.cz', 'robot Promyka.cz');
        $mail->addBcc("tomas.rokos@gmail.com","tomas.rokos@gmail.com");
        if (is_array($tos)) {
            foreach ($tos as $to) {
                $mail->addTo($to,$to);
            }
        } else if ($tos != NULL) {
            $mail->addTo($tos,$tos);
        }
        $mail->setSubject($subject);
        $mail->setBodyHtml($html);
        try {
            $mail->send($tr);
        } catch (Exception $e) {

        }

    }

}