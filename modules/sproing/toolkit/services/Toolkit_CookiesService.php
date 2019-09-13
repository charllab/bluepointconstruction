<?php

namespace Craft;

class Toolkit_CookiesService extends BaseApplicationComponent
{
    public function set($name = "", $value = "", $expire = 0, $path = "/", $domain = "", $secure = false, $httponly = false)
    {
        $expire = (int)$expire;

        if ($value == "") {
            $expire = (int)(time() - 3600);
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        $_COOKIE[$name] = $value;
    }

    public function get($name = "")
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
    }
}
