<?php
require_once('app/config.php');
require_once('app/user.php');

if( count($_GET) == 0){
    header('Location: index.php');
}

if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id' => $vk_app_id,
        'client_secret' => $vk_api_secure_key,
        'code' => $_GET['code'],
        'redirect_uri' => $vk_api_redirect_uri
    );
    $url_with_params = $vk_api_token_url . '?' . urldecode(http_build_query($params));
    $content = file_get_contents($url_with_params);

    $token = json_decode($content, true);

    if (isset($token['access_token'])) {
        $params = array(
            'uids' => $token['user_id'],
            'fields' => 'uid,first_name,last_name',
            'access_token' => $token['access_token']
        );
        $user_info_url = $vk_api_user_info_url . '?' . urldecode(http_build_query($params));
        $content = file_get_contents($user_info_url);
        $user_info = json_decode($content, true);
        if (isset($user_info['response'][0]['uid'])) {
            $user_info = $user_info['response'][0];
            $result = true;
        }
    }

    if ($result) {
        try {
            $user = new User(User::ACCOUNT_TYPE_VK, $user_info['uid'], $user_info['first_name'], $user_info['last_name']);
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