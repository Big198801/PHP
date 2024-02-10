<?php

namespace app\engine;

class Auth
{
    protected $user = null;

    public function __construct(Session $session)
    {
        $this->session = $session;    
    } 
   

    public function deleteCookieHash() {
        setcookie('hash', '', time()-3600, '/');    
    }
    
    public function logout() {
        $this->deleteCookieHash();
        $this->clearDataAuthInSession();
    }

    
    protected function updateDataAuthInSession() {
        if (!$this->user) {
            return;
        }
        $this->session->login = $this->user->login;
        $this->session->userName = $this->user->name;
        $this->session->userId = $this->user->id;
        $this->session->isAdmin = $this->user->is_admin;
        
    }

    protected function clearDataAuthInSession() {
        $this->session->deleteParam('login');
        $this->session->deleteParam('userName');
        $this->session->deleteParam('userId');
        $this->session->deleteParam('isAdmin');

    }

    public function getUserInfo() {
        return [
            'userName' => $this->session->userName,
            'login' => $this->session->login,
            'userId' => $this->session->userId,
        ];  
    }
    

    public function checkCookieHash() {
        if (!isset($this->session->login) && isset($_COOKIE['hash'])) {
            $user = \Users::where('cookie_hash', $_COOKIE['hash'])->first();
            if ($user && !empty($user->login)) {
                $this->user = $user;
                $this->updateDataAuthInSession();
            } else {
                $this->clearDataAuthInSession();
            }
        }
    }

    public function isAdmin() {
        $this->checkCookieHash();
        return isset($this->session->login) && $this->session->isAdmin;
    }

    public function isAuth() {
        $this->checkCookieHash();
        return isset($this->session->login);
    }
    
    public function updateHashUser($hash = '') {
        if ($this->user) {
            $this->user->cookie_hash = $hash;
            \Users::save($this->user);
            setcookie("hash", $hash, time() + (isset($hash) ? 3600 : -3600), '/');
        }
    }

  
    public function getUserByLoginPassword($login, $pass) {
        $user = \Users::where('login', $login)->first();
        if ($user && password_verify($pass, $user->password_hash)) {
            return $user;
        } else NULL;
    }
    

    public function auth($login, $pass, $save = false) {
        $this->user = $this->getUserByLoginPassword($login, $pass);
        if ($this->user) {
            $this->updateDataAuthInSession();
            $hash = ($save) ? uniqid(rand(), true) : "";  
            $this->updateHashUser($hash); 
            return true;
        };

        return false;
    }
    
    public function updateUserInfo() {
        if (isset($this->user) && $this->user->getKeyValue()) {
            $this->user = \Users::find($this->user->getKeyValue());
            updateDataAuthInSession();
        }
    }

    public function isLoginExist($login) {
        return \Users::isLoginExist($login);  
    }

}