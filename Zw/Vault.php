<?php

class ZwVault
{
    static public function path($section,$id)
    {
        $upper_id = (int)($id / 1000);
        return '/vault/'.$section.'/'.$upper_id.'/'.$id.'/';
    }
    static public function base_path()
    {
        $path = realpath($_SERVER["DOCUMENT_ROOT"].'/../..');
        if ($path[strlen($path)-1] != '\\')
            $path .= '/';
        return $path;
    }
    static public function full_path($section,$id)
    {
        return self::base_path().self::path($section,$id);
    }
    static public function save($source,$filename,$section,$id)
    {
        $path = self::createVault($section,$id);
        move_uploaded_file($source,$path.$filename);
    }
    static public function createVault($section,$id)
    {
        $result = self::path($section,$id);
        $parts = explode('/',$result);
        $path = self::base_path();
        for($i=0;$i<count($parts)-1;$i++) {
            if (strlen($parts[$i]) == 0)
                continue;
            $path .= $parts[$i].'/';
            if (file_exists($path) == true)
                continue;
            mkdir($path,0755);
            chmod($path,0755);
        }
        return $path;
    }
}