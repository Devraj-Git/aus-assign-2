<?php

if(!function_exists('config')) {
    function config(string $key) {
        require BASEPATH . "/config/settings.php";

        if(key_exists($key, $config)) {
            return $config[$key];
        } else {
            return  false;
        }
    }
}
 
if(!function_exists('view')) {
    function view(string $view, ?array $data = null) {
        new App\System\Core\Views($view, $data);
    }
}

if(!function_exists('url')) {
    function url(string $uri = ''): string {
        return config('app_url').$uri;
    }
}

if(!function_exists('redirect')) {
    function redirect(string $url) {
        header("Location: $url");
        die;
    }
}

if(!function_exists('set_message')) {
    function set_message(string $content, string $type = 'info') {
        $_SESSION['message'] = compact('content', 'type');
    }
}

if(!function_exists('set_session')) {
    function set_session(string $name, string $content) {
        $_SESSION[$name] = $content;
    }
}

if(!function_exists('get_message')) {
    function get_message() {
        return !empty($_SESSION['message']) ? $_SESSION['message'] : false;
    }
}

if(!function_exists('get_session')) {
    function get_session(string $name) {
        return !empty($_SESSION[$name]) ? $_SESSION[$name] : false;
    }
}

if(!function_exists('clear_message')) {
    function clear_message() {
        unset($_SESSION['message']);
    }
}

if(!function_exists('clear_session')) {
    function clear_session(string $name) {
        if(get_session($name))
        unset($_SESSION[$name]);
    }
}

if(!function_exists('authenticate')) {
    function authenticate(): bool {
        if(!empty($_SESSION['user_id'])) {
            return true;
        } elseif(!empty($_COOKIE['User_Log'])) {
            $_SESSION['user_id'] = $_COOKIE['User_Log'];

            return true;
        }

        return false;
    }
}


if(!function_exists('now')) {
    function now(string $format = 'Y-m-d H:i:s'): string {
        return date($format);
    }

    if(!function_exists('dt_format')) {
        function dt_format(string $dt, string $format = 'j M Y h:i A'): string {
            return date($format, strtotime($dt));
        }
    }
}

if(!function_exists('encrypt')) {
    function encrypt($data) {
        $method = 'AES-256-CBC';
        $key = substr(config('ENCRYPTION_KEY'), 0, 32);
        $iv = substr(config('ENCRYPTION_IV'), 0, 16);

        $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
        return base64_encode($encrypted);
    }
}

if(!function_exists('decrypt')) {
    function decrypt($data) {
        $method = 'AES-256-CBC';
        $key = substr(config('ENCRYPTION_KEY'), 0, 32);
        $iv = substr(config('ENCRYPTION_IV'), 0, 16);
        $data = base64_decode($data);
        $decrypted = openssl_decrypt($data, $method, $key, 0, $iv);
        return $decrypted;
    }
}

if(!function_exists('goback')) {
    function goback() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

if(!function_exists('get_action')) {
    function get_action() : string {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $requestUri = $_SERVER['REQUEST_URI'];
        return $scheme . '://' . $host . $requestUri;
    }
}
