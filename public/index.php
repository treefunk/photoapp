<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../private/includes/header.php'; ?>
<?php require_once '../private/init.php'; ?>
<body>

    <?php if(someoneIsLoggedIn()): ?>
        Logged in as <?=getSession('username');?>
    <?php endif; ?>


    <div class="content">
    <h1>Photo App</h1>






    <?php  if(!someoneIsLoggedIn()): ?>
        <a href="users/new.php">Register</a>    
        <a href="login.php">Log in</a>
    <?php else: ?>
        <a href="photos/new.php">Upload Photo</a>
        <a href="logout.php">Log out</a>
    <?php endif; ?>
    

    </div>
</body>
<?php require_once "../private/includes/footer.php"; ?>