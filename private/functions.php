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
            //$stmt->execute();
        }
        else{
            echo "something went wrong";
            
        }
    }
    else{
        return false;
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
    $all_users = [];
    $sql = "SELECT users.* FROM users 
            INNER JOIN user_photo ON users.id = user_photo.user_id 
            WHERE user_photo.photo_id = '$photoID'";
    $result = $db->query($sql);
    if(!$result)
        return false;
    while($user = $result->fetch_assoc()){
        $users[$user['id']] = $user['user_name'];
    }
    $sql = "SELECT * FROM users";
    $r = $db->query($sql);

    while($u = $r->fetch_assoc()){
        $all_users[$u['id']] = $u['user_name'];
    }

    $new = array_diff_assoc($all_users,$users);
    unset($new[getSession('id')]);
    mysqli_free_result($result);
    return $new;
}

function sharePhotoTo($target_id,$photo_id){
    global $db,$errors;
    $sql = "INSERT INTO user_photo (user_id,photo_id) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ii',$target_id,$photo_id);
    if($stmt->execute()){
        return true;
    }else{
        return false;
    }
}

function photosSharedWithThisID($id){
    global $db, $errors;
    $sql = "SELECT photos.* FROM photos INNER JOIN user_photo ON photos.id = user_photo.photo_id WHERE user_photo.user_id = '$id'";
    
    $r = $db->query($sql);
    $photos = [];
    if($r){
        while($photo = $r->fetch_assoc()){
            $photos[] = $photo;
        }
    }else{
        echo "something went wrong";
    }
    mysqli_free_result($r);
    return $photos;
}

function photoIdToUsername($id){
    global $db,$errors;
    $sql = "SELECT users.user_name FROM users WHERE id = '$id'";
    $r = $db->query($sql);
    if($r){
        $photo = $r->fetch_assoc();
        $username = $photo['user_name'];
        return $username;
    }
    return "gg";
}

function downloadPhoto($id){
    global $db,$errors;
    $sql = "SELECT * FROM photos WHERE id = '$id'";
    $r = $db->query($sql);
    if($r){
        $photo = $r->fetch_assoc();
        $file = __DIR__."/uploads/".$photo['uploaded_by']."_".$photo['name'];
        echo $file;
        ob_clean();
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="'.$photo['name'].'"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            set_time_limit(0);
            // $file = fopen($file,"r");
            // while(!feof($file)){
            //     print(fread($file,1024*8));
            // }


            exit;
        }else{
            echo "file not exists";
        }
    }else{
        return false;
    }
}