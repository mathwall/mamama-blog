<?php

namespace App\Src;

class Session {

    static public function write($key_string, $value){
        $keys = explode('.', $key_string);
        if (count($keys) < 2) {
            $_SESSION[$keys[0]] = $value;
            return;
        }

        $dest = &$_SESSION;
        for($i = 0; $i < count($keys); $i++) {
            if (isset($dest[$keys[$i]])) {
                if (!is_array($dest[$keys[$i]])) {
                    $dest[$keys[$i]] = [];
                }
            } else {
                $dest[$keys[$i]] = [];
            }
            $dest = &$dest[$keys[$i]];
        }
        $dest = $value;
    }

    static public function read($key_string){
        $keys = explode('.', $key_string);
        if (count($keys) < 2) {
            return $_SESSION[$keys[0]];
        }

        $dest = &$_SESSION;
        for($i = 0; $i < count($keys); $i++) {
            if (isset($dest[$keys[$i]])) {
                if (!is_array($dest[$keys[$i]])) {
                    if ($i !== count($keys)-1) {
                        return null;
                    }
                }
            } else {
                return null;
            }
            $dest = &$dest[$keys[$i]];
        }
        return $dest;
    }

    static public function load(){
        session_start();
    }

    static public function destroy(){
        session_unset();
        session_destroy();
        session_reset();
    }
}