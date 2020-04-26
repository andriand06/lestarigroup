<?php
class input {
    public static function get($item) {
        if (isset($_POST[$item])){
            return trim($_POST[$item]);
        } else if (isset($_GET[$item])) {
            return trim($_GET[$item]);
        }
        return '';
    }

    public static function runSanitize($value,$sanitizeType){
        switch ($sanitizeType) {
            case 'string' :
               filter_var($value,FILTER_SANITIZE_STRING);
            break;
            case 'email' :
                filter_var($value,FILTER_SANITIZE_EMAIL);
            break;
            case 'url' :
                filter_var($value,FILTER_SANITIZE_URL);
        }
        return $sanitizeType;
    }
   
}