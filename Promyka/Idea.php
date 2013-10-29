<?php

class PromykaIdea
{

    static public function create($idea,$url,$project,$status = 0,$support = 0,$reporter = '')
    {
        $data = array();
        $data['url'] = ZwString::safe_string($url);
        $data['idea'] = ZwString::safe_string($idea);
        if ($reporter == '') {
            $email = ZwUser::id_static();
            $reporter = $email == null ? '' : $email;
        }
        $data['reporter'] = $reporter;
        $data['id_project'] = $project;
        $data['status'] = $status;
        $data['support'] = $support;
        $db = ZwApplication::getInstance()->db();
        $db->insert('ideas.ideas',$data);
        $id = $db->lastInsertId();

        self::log($id,0,'idea vytvořena');
        PromykaEmail::send('tomas.rokos@gmail.com','Nová idea #'.$id.' pro projekt '.$project,$idea);

        return $id;

    }
    public static function log($idea,$action,$text)
    {
        $data = array();
        $data['id_user'] = 0;//$user->has_user() ? $user->id_user() : NULL;
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['id_idea'] = $idea;
        $data['timestamp'] = date("c");
        $data['text'] = $text;
        $data['action'] = $action;
        ZwApplication::getInstance()->db()->insert("ideas.log",$data);
    }
}
