<?php

function add_user($user)
{
    global $db;
    $user['password'] = password_hash($user['password'],PASSWORD_BCRYPT);
    $sql = "INSERT INTO users ";
    $sql.= "(first_name,last_name,user_name,password) VALUES ";
    $sql.= "(?,?,?,?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssss",$user['first_name'],$user['last_name'],$user['username'],$user['password']);
    return $stmt->execute() ? true : false;
}

function authenticate_user($username,$password)
{
    global $db, $errors;

    $sql = "SELECT * FROM users WHERE ";
    $sql.= "user_name = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 0){
        $errors[] = "INVALID USERNAME";
    }
    $user = $result->fetch_assoc();
    if(password_verify($password,$user['password'])){
        return true;
    }else{
        return false;
    }
}