<?php
require_once('config.php');
require_once('lib.php');

class User
{
    const ACCOUNT_TYPE_EMPTY = 0;
    const ACCOUNT_TYPE_VK = 1;
    const ACCOUNT_TYPE_FACEBOOK = 2;
    const ACCOUNT_TYPE_GOOGLE = 3;

    function __construct($account_type, $uid, $first_name, $last_name)
    {
        // ACCOUNT_TYPE_GOOGLE not implemented yet
        if ($account_type != self::ACCOUNT_TYPE_VK AND 
            $account_type != self::ACCOUNT_TYPE_FACEBOOK AND
            $account_type != self::ACCOUNT_TYPE_GOOGLE) {
            throw new Exception("Unsupported account type!");
        }

        $this->id = 0;
        $this->uid = $uid;
        $this->account_type = $account_type;
        $this->first_name = $first_name;
        $this->last_name = $last_name;

        // save user to DB if user doesn't exist
        if (!$this->isExist()) {
            $this->saveToDb();
        }
    }

    function authenticate()
    {
        $_SESSION['user'] = (array)$this;
    }

    static function isAuthenticate()
    {
        $result = false;
        
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $result = true;
        }
        
        return $result;
    }

    function isExist()
    {
        $result = false;
        
        $sql_query = "SELECT * FROM users where account_type = $this->account_type AND uid = $this->uid LIMIT 1";
        $stmt = mysqli_prepare(DB::getInstance(), $sql_query);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row) {
            $this->id = $row['id'];
            // reset user name by db values
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $result = true;
        }
        
        return $result;
    }

    function saveToDb()
    {
        $sql_query = "INSERT INTO users (uid,account_type,first_name,last_name) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare(DB::getInstance(), $sql_query);
        mysqli_stmt_bind_param($stmt, 'ssss', $this->uid, $this->account_type, $this->first_name, $this->last_name);
        if (mysqli_stmt_execute($stmt)) {
            $id = mysqli_stmt_insert_id($stmt);
            $this->id = $id;
        }
    }
    
    static function getAttributes()
    {
        $result = [];
        
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $result['user_id'] = $user['id'];
            $result['account_type'] = $user['account_type'];
            $result['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $result['first_name'] = $user['first_name'];
            $result['last_name'] = $user['last_name'];
        }
        
        return $result;
    }
}