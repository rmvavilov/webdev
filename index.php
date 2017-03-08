<?php
require_once('app/config.php');
require_once('app/user.php');

if (User::isAuthenticate()) {
    header('Location: messages.php');
}

include('templates/index.php');