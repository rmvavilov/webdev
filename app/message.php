<?php
require_once('config.php');

class Message
{
    const MESSAGE_TYPE = 0; // message
    const COMMENT_TYPE = 1; // comment of message/comment of comment

    function __construct($user_id, $type, $parent_id, $text)
    {
        $created_at = date('Y-m-d H:i:s');

        $this->id = 0;
        $this->user_id = $user_id;
        $this->type = $type;
        $this->parent_id = $parent_id;
        $this->text = $text;
        $this->created_at = $created_at;
    }

    public function save()
    {
        $sql_query = "INSERT INTO messages (user_id,type,parent_id,text,created_at) VALUES (?,?,?,?,?)";
        $stmt = mysqli_prepare(DB::getInstance(), $sql_query);
        mysqli_stmt_bind_param($stmt, 'iiiss', $this->user_id, $this->type, $this->parent_id, $this->text, $this->created_at);
        if (mysqli_stmt_execute($stmt)) {
            $id = mysqli_stmt_insert_id($stmt);
            $this->id = $id;
        }
    }

    public static function get($message_id)
    {
        $result = [];
        $query_result = mysqli_query(DB::getInstance(), "SELECT * FROM messages where id = $message_id LIMIT 1");
        $rows = mysqli_fetch_assoc($query_result);
        if ($rows) {
            $result = $rows;
        }
        return $result;
    }

    public static function getAll()
    {
        $result = [];
        $query_result = mysqli_query(DB::getInstance(), "select messages.*, users.first_name, users.last_name from messages" .
            " left join users on messages.user_id = users.id" .
            " ORDER BY messages.created_at ASC");
        while ($row = mysqli_fetch_assoc($query_result)) {
            $rows[] = $row;
        }
        if ($rows) {
            $result = $rows;
        }

        return $result;
    }

    public static function updateText($id, $text)
    {
        $message = self::get($id);
        $user = User::getAttributes();
        if ($message AND $user AND $message['user_id'] == $user['user_id']) {
            $sql_query = "UPDATE messages SET text = ? where id = ?";
            $stmt = mysqli_prepare(DB::getInstance(), $sql_query);
            mysqli_stmt_bind_param($stmt, 'ss', $text, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}