<?php

class ZwUser
{
    const SESSIONKEY = 'ZwUser';
    const SESSIONKEYID = 'id';
    const SESSIONKEYADMIN = 'admin';

    private $_session;
    public function __construct()
    {
        $this->_session = ZwApplication::getInstance()->session();
    }

    public function login($id,$admin =false)
    {
        $this->_session->set(self::SESSIONKEY,array(self::SESSIONKEYID => $id,self::SESSIONKEYADMIN => $admin));
    }
    public function logout()
    {
        $this->_session->remove(self::SESSIONKEY);
    }
    static public function id_static()
    {
        $user = new self;
        return $user->id();
    }
    public function id()
    {
        $var = $this->_session->get(self::SESSIONKEY);
        return $var === null ? null : $var[self::SESSIONKEYID];
    }
    public function is_admin()
    {
        $var = $this->_session->get(self::SESSIONKEY);
        return $var === null ? null : $var[self::SESSIONKEYADMIN];
    }

    public function acl($group,$action,$additional_info = null)
    {
        if ($this->id() === null)
            return false;
        if ($this->is_admin() == true)
            return true;
        $method_name = 'acl_'.$group;
        if (method_exists($this,$method_name)) {
            return $this->$method_name($action,$additional_info);
        }
        return false;
    }

}