<?php
session_start();

set_error_handler('errorHandler');
function errorHandler($error_number, $error_message, $filename, $error_line_number)
{
    $date = date('Y-m-d H:i:s (T)');
    $f = fopen('error.txt', 'a');
    if (!empty($f)) {
        $filename = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename);
        $err = "$date: $error_message = $filename = $error_line_number\r\n";
        fwrite($f, $err);
        fclose($f);
    }
}

// read main config file
$config_file = 'config.ini';
$config = parse_ini_file($config_file);
if (!$config) {
    echo 'Could not read config file - please check the config file';
    die();
}

// DB PARAMETERS
define('DB_HOST', $config['db_host']);
define('DB_USERNAME', $config['db_username']);
define('DB_PASSWORD', $config['db_password']);
define('DB_NAME', $config['db_name']);

class DB
{
    private static $instance = null;

    static public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            mysqli_set_charset(self::$instance, "utf8");
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}

// VK API parameters
$vk_app_id = $config['vk_app_id'];
$vk_api_secure_key = $config['vk_api_secure_key'];
$vk_api_redirect_uri = $config['vk_api_redirect_uri'];
$vk_api_url = $config['vk_api_url'];
$vk_api_token_url = $config['vk_api_token_url'];
$vk_api_user_info_url = $config['vk_api_user_info_url'];
$vk_api_parameters = array(
    'client_id' => $vk_app_id,
    'redirect_uri' => $vk_api_redirect_uri,
    'response_type' => 'code'
);

// Facebook API parameters
$facebook_app_id = $config['facebook_app_id'];
$facebook_api_secure_key = $config['facebook_api_secure_key'];
$facebook_api_redirect_uri = $config['facebook_api_redirect_uri'];
$facebook_api_url = $config['facebook_api_url'];
$facebook_api_token_url = $config['facebook_api_token_url'];
$facebook_api_user_info_url = $config['facebook_api_user_info_url'];
$facebook_api_parameters = array(
    'client_id' => $facebook_app_id,
    'redirect_uri' => $facebook_api_redirect_uri,
    'response_type' => 'code',
    'scope' => 'public_profile'
);

// Google API parameters
$google_app_id = $config['google_app_id'];
$google_api_secure_key = $config['google_api_secure_key'];
$google_api_redirect_uri = $config['google_api_redirect_uri'];
$google_api_url = $config['google_api_url'];
$google_api_token_url = $config['google_api_token_url'];
$google_api_user_info_url = $config['google_api_user_info_url'];

$google_api_profile_scope = ' https://www.googleapis.com/auth/userinfo.profile';
$google_api_email_scope = 'https://www.googleapis.com/auth/userinfo.email';

$google_api_parameters = array(
    'redirect_uri'  => $google_api_redirect_uri,
    'response_type' => 'code',
    'client_id'     => $google_app_id,
    'scope'         => $google_api_profile_scope
);