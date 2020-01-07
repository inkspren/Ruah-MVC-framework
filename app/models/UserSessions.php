<?php
namespace App\Models;
use Core\Model;
use Core\Session;
use Core\Cookie;

class UserSessions extends Model {

    public $id, $user_id, $session, $user_agent; //table columns, has to  be declared in every model

    public function __construct() {
        $table = 'user_sessions';
        parent::__construct($table);
    }

    public static function getFromCookie() {
        $userSession = new self(); //instantiate new object if cookie exists
        if(Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
            $userSession = $userSession->findFirst([
                'conditions' => "user_agent = ? AND session = ?",
                'bind' => [Session::uagent_no_version(), Cookie::get(REMEMBER_ME_COOKIE_NAME)]
            ]);
        }
        if(!$userSession) return false;//if the user session doesn't exist
        return $userSession;
    }
}