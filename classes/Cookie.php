<?php
class Cookie {
    public static function exists($naam) {
        return(isset($_COOKIE[$naam])) ? true : false;
    }
    public static function get($naam) {
        return $_COOKIE[$naam];
    }

    public static function put($naam, $value, $expiry) {
        if(setcookie($naam, $value, time() + $expiry, '/')) {
            return true;
        }
        return false;
    }

    public static function delete($naam) {
        self::put($naam, '', time() - 1);
    }
}