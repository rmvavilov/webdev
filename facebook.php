<?php
require_once('app/config.php');
require_once('app/user.php');

if( count($_GET) == 0){
    header('Location: index.php');
}

if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id' => $facebook_app_id,
        'client_secret' => $facebook_api_secure_key,
        'code' => $_GET['code'],
        'redirect_uri' => $facebook_api_redirect_uri
    );
    $url_with_params = $facebook_api_token_url . '?' . urldecode(http_build_query($params));
    $content = file_get_contents($url_with_params);
    // Warning: use only parse_str on get token step,
    // because facebook return string NOT JSON
    parse_str($content, $token);

    if (isset($token['access_token'])) {
        $params = array(
            'access_token' => $token['access_token'],
            'fields' => 'id,first_name,last_name'
        );
        $user_info_url = $facebook_api_user_info_url . '?' . urldecode(http_build_query($params));
        $content = file_get_contents($user_info_url);
        $user_info = json_decode($content, true);
        if (isset($user_info['id'])) {
            $result = true;
        }
    }

    if ($result) {
        try {
            $user = new User(User::ACCOUNT_TYPE_FACEBOOK, $user_info['id'], $user_info['first_name'], $user_info['last_name']);
        } catch (Exception $e) {
            header('Location: index.php');
        }
        $user->authenticate();

        // Redirect auth user to messages page
        header('Location: messages.php');
    } else {
        header('Location: index.php');
    }
}