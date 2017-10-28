<?php

function add_user($user)
{
    global $db,$errors;
    if(strcmp($user['password'],$user['confirm_password'])){
        $errors[] = "password is not the same!";
        return false;
    }
    $user['password'] = password_hash($user['password'],PASSWORD_BCRYPT);
    $sql = "INSERT INTO users ";
    $sql.= "(first_name,last_name,user_name,password) VALUES ";
    $sql.= "(?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssss",$user['first_name'],$user['last_name'],$user['username'],$user['password']);
    $stmt->execute();

    switch($stmt->errno){
        case 1062:
            $errors[] = "Username is already taken.";
            break;
        default:
            break;
    }
    return 0;
}

function authenticate_user($username,$password,$admin = false)
{
    global $db,$errors;
    $sql = "SELECT * FROM users WHERE ";
    $sql.= "user_name = ? ";
    if($admin){ $sql.= "AND admin = 1"; }
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if(password_verify($password,$user['password'])){
        unset($user['password']);
        storeSession([
            'messsage' => 'Successfully Logged in',
            'id' => $user['id'],
            'username' => $user['user_name']
        ]);
        return $user;
    }else{
        $errors[] = "invalid username or password";
        return false;
    }
}

function redirect_to($url)
{
    header("Location: ".$url);
}

function storeSession($var)
{
    foreach($var as $key=>$val){
        $_SESSION[$key] = $val;
    }

}

function getSession($key)
{
    return $_SESSION[$key] ?? false;
}

function getSessionAndDestroy($key)
{
    $value = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $value;
}

function someoneIsLoggedIn()
{
    return getSession('id') ?? false;
}