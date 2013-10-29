<?php
include_once '3rdparty/LightOpenID.php';

class ZwAuthGoogle
{
    const EMAIL = 'contact/email';
    const FIRSTNAME = 'namePerson/first';
    const LASTNAME = 'namePerson/last';
    /**
     * when returns false it's necessary to pass it further for authentication run
     * @return null|array
     */
    static public function authenticate()
    {
        try {
            $openid = new LightOpenID;
            if (!$openid->mode) {
                $openid->identity = 'https://www.google.com/accounts/o8/id';
                $openid->required = array(self::FIRSTNAME, self::LASTNAME, self::EMAIL);
                header('Location: ' . $openid->authUrl());
                die('');
            } else if($openid->mode == 'cancel') {
                return null;
            } else {
                if($openid->validate()) {
                    $attributes = $openid->getAttributes();
                    return $attributes;
                } else {
                }
                return null;
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
            die('');
        }
    }
}