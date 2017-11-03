

<?php $pageTitle = "Photo App - Login"; ?>
<?php require_once '../private/init.php'; ?>
<?php require_once PRIVATE_PATH.'/includes/header.php'; ?>

<?php
if($_POST){
    if(authenticate_user($_POST['user_name'],$_POST['password'])){
        redirect_to('index.php');
    }
}
?>

<div class="content">
<?php foreach($errors as $error): ?>
<li><?=$error?></li>
<?php endforeach; ?>
<form action="" method="post">
    <label for="Username">Username:</label>
    <input type="text" name="user_name" id="Username"><br />
    <label for="password">Password:</label>
    <input type="password" name="password" id="password"><br />
    <button type="submit">Submit</button>
</form>
<a href="users/new.php">Register</a>    
</div>

<?php require_once PRIVATE_PATH.'/includes/footer.php'; ?>