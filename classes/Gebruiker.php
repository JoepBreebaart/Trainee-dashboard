<?php

class Gebruiker {
    private $_db;
    private $_data,
            $_sessionName,
            $_cookieName,
            $_isLoggedIn;


    public function __construct($gebruiker = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        if(!$gebruiker) {
            if(Session::exists($this->_sessionName)) {
                $gebruiker = Session::get($this->_sessionName);

                if($this->find($gebruiker)) {
                    $this->_isLoggedIn = true;
                } else {
                    //proces logged out
                }
            }
        } else {
            $this->find($gebruiker);
        }
    }

    public function update($fields = array(), $id = null) {

        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }


        if(!$this->_db->update('gebruikers', $id, $fields)) {
            throw new Exception('er was een probleem met updaten(creeren) van een account');
        }
    }

    public function create($fields = array()) {
        if(!$this->_db->insert('gebruikers', $fields)) {
            throw new Exception('Er was een probleem bij het aanmelden!');
        }
    }

    public function find($gebruiker = NULL) {
        if($gebruiker) {
            $field = is_numeric($gebruiker) ? 'id' : 'email';
            $data = $this->_db->get('gebruikers', array($field, '=', $gebruiker));

            if($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }
    
    public function login($email = NULL, $wachtwoord = NULL, $remember = false) {
        if(!$email && !$wachtwoord && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
        } else {
            $gebruiker = $this->find($email);

            if ($gebruiker) {
                if ($this->data()->wachtwoord === Hash::make($wachtwoord, $this->data()->salt)) {
                    Session::put($this->_sessionName, $this->data()->id);

                    if ($remember) {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('gebruikers_session', array('gebruiker_id', '=', $this->data()->id));

                        if (!$hashCheck->count()) {
                            $this->_db->insert('gebruikers_session', array(
                                'gebruiker_id' => $this->data()->id,
                                'hash' => $hash
                            ));
                    } else {
                        $hash = $hashCheck->first()->hash;
                    }

                    Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                }

                return true;
            }
        }
    }
    return false;
}

    public function hasPermission($key) {
        $groep = $this->_db->get('groepen', array('id', '=', $this->data()->groep));

        if($groep->count()) {
           $permissions = json_decode($groep->first()->bevoegdheid, true);
           
           if($permissions[$key] == true) {
                return true;
           } else {
               return false;
           }
        }
    }

    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    public function logout() {

        $this->_db->delete('gebruikers_session', array('gebruiker_id', '=', $this->data()->id));

        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    public function data() {
        return $this->_data;
    }

    public function isLoggedIn() {
        return $this->_isLoggedIn;
    }
}