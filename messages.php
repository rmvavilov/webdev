<?php
require_once('app/config.php');
require_once('app/lib.php');
require_once('app/user.php');
require_once('app/message.php');

if (User::isAuthenticate()) {
    $guest_mode = false;
    $user = $_SESSION['user'];
} else {
    $guest_mode = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include('templates/messages.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $user['id'];
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            // get all messages
            case 0:
                $messages = Message::getAll();
                $message_tree = buildTree($messages, 0);
                $data = [
                    'guest' => $guest_mode,
                    'user_id' => $user_id,
                    'message_tree' => $message_tree,
                ];
                echo json_encode($data);
                break;

            // create new message
            case 2:
                if ($guest_mode) {
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(['success' => false, 'auth' => false]);
                    die();
                }
                $parent_id = trim(strip_tags($_POST['parent_id']));
                $type = trim(strip_tags($_POST['type']));
                $text = trim(strip_tags($_POST['text']));
                if ($type != 0 AND $type != 1) {
                    echo json_encode(['success' => false, 'msg' => 'Wrong message type']);
                    die();
                }
                $result = [];
                $message = new Message($user_id, $type, $parent_id, $text);
                $message->save();
                if ($message->id) {
                    $message_arr = (array)$message;
                    $user_attributes = User::getAttributes();
                    $result = array_merge($message_arr, $user_attributes);
                }
                echo json_encode($result);
                break;

            // update message
            case 3:
                if ($guest_mode) {
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(['success' => false, 'auth' => false]);
                    die();
                }
                $parent_id = trim(strip_tags($_POST['parent_id']));
                $text = trim(strip_tags($_POST['text']));
                $message = Message::get($parent_id);

                Message::updateText($parent_id, $text);
                $result = [
                    'id' => $message['id'],
                    'text' => $text
                ];
                echo json_encode($result);
                break;
        }
    }
}