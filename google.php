<?php
require_once('app/config.php');
require_once('app/user.php');

if( count($_GET) == 0){
    header('Location: index.php');
}

if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id' => $google_app_id,
        'client_secret' => $google_api_secure_key,
        'redirect_uri' => $google_api_redirect_uri,
        'grant_type' => 'authorization_code',
        'code' => $_GET['code']
    );
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $google_api_token_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);
    $token = json_decode($result, true);

    if (isset($token['access_token'])) {
        $params = array(
            'access_token' => $token['access_token']
        );
        $user_info_url = $google_api_user_info_url . '?' . urldecode(http_build_query($params));
        $content = file_get_contents($user_info_url);
        $user_info = json_decode($content, true);
        if (isset($user_info['id'])) {
            $user_info = $user_info;
            $result = true;
        }
    }

    if ($result) {
        try {
            $user = new User(User::ACCOUNT_TYPE_GOOGLE, $user_info['id'], $user_info['given_name'], $user_info['family_name']);
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