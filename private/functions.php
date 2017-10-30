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
    if($stmt->execute()){
        return true;
    }
    return false;
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

function uploadFile($file,$post){
    global $db,$errors;
    $photoName = $file['name'];
    $photoTmp = $file['tmp_name'];
    $id = getSession('id');
    $uploadPath = '../../private/uploads/';
    if(!file_exists($uploadPath)){
        mkdir($uploadPath);
    }
    if(file_exists($uploadPath.getSession('id')."_".$photoName)){
        $errors[] = "you have already uploaded that file!";
    }
    if(move_uploaded_file($photoTmp,$uploadPath.getSession('id').'_'.$photoName ) && empty($errors)){
        $sql = "INSERT INTO photos (name,caption,uploaded_by) VALUES ";
        $sql.= "(?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssi',$photoName,$post['caption'],$id);
        if($stmt->execute()){
            $photoid = $db->insert_id;
            echo "successfully uploaded";
            $sql = "INSERT INTO user_photo (user_id,photo_id) VALUES (?,?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ii',$id,$photoid);
            $stmt->execute();
        }
        else{
            echo "something went wrong";
            
        }
    }
    else{
        print_r($errors);
    }
}

function getAllPhotosforId($id){
    global $db,$errors;
    $photos = [];
    $sql = "SELECT * FROM photos WHERE uploaded_by = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result){
        while($photo = $result->fetch_assoc()){
            $photos[] = $photo;
        }
    }else{
        return false;
    }
    return $photos;
}

function getAllUsersToShareWith($photoID){
    global $db,$errors;
    $users = [];
    $sql = "SELECT users.* FROM users 
            INNER JOIN user_photo ON users.id <> user_photo.user_id 
            WHERE user_photo.photo_id <> '$photoID'";
    $result = $db->query($sql);
    if(!$result)
        return false;
    while($user = $result->fetch_assoc()){
        $users[] = $user;
    }
    mysqli_free_result($result);
    return $users;
}