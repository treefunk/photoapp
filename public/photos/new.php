<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../../private/init.php'; ?>
<?php require_once PRIVATE_PATH.'/includes/header.php'; ?>


<?php
if($_POST){
    $photoName = $_FILES['photo']['name'];
    $photoTmp = $_FILES['photo']['tmp_name'];
    $id = getSession('id');
    $uploadPath = 'uploads/';
    if(!file_exists('uploads/')){
        mkdir('uploads');
    }
    if(file_exists('uploads/'.getSession('id')."_".$photoName)){
        $errors[] = "you have already uploaded that file!";
    }
    if(move_uploaded_file($photoTmp,$uploadPath.getSession('id').'_'.$photoName ) && empty($errors)){
        $sql = "INSERT INTO photos (name,caption,uploaded_by) VALUES ";
        $sql.= "(?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('ssi',$photoName,$_POST['caption'],$id);
        if($stmt->execute()){
            echo "successfully uploaded";
        }
        else{
            echo "something went wrong";
            
        }
    }
    else{
        print_r($errors);
    }
}
?>




<form action="" method="post" enctype="multipart/form-data">
    <label for="photo">Photo:</label><input type="file" name="photo" id="photo">
    <label for="caption">caption</label><input type="text" name="caption" id="caption">
    <button type="submit">Submit</button>
    <div class=""></div>
</form>

<?php require_once PRIVATE_PATH.'/includes/footer.php'; ?>