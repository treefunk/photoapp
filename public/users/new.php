<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../../private/init.php'; ?>
<?php require_once PRIVATE_PATH.'/includes/header.php'; ?>

<?php
    foreach($_POST as $key => $val){
        $user[$key] = $val;
    }

    if($_POST){
        echo add_user($_POST);
    }
    authenticate_user('Td','123');


?>
<?php foreach($errors as $error): ?>
<li><?php echo $error; ?></li>
<?php endforeach; ?>

<h1>Create User</h1>

<form action="" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="<?php echo $user['username'] ?? ""; ?>"><br />
    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" id="first_name" value="<?php echo $user['first_name'] ?? ""; ?>"><br />
    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" id="last_name" value="<?php echo $user['last_name'] ?? ""; ?>"><br />
    <label for="password">Password:</label>
    <input type="password" name="password" id="password"><br />
    <button type="submit">Submit</button>
</form>

<?php require_once PRIVATE_PATH."/includes/footer.php"; ?>