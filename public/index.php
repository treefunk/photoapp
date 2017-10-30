<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../private/includes/header.php'; ?>
<?php require_once '../private/init.php'; ?>
<body>

    <?php if(someoneIsLoggedIn()): ?>
        Logged in as <?=getSession('username');?>
    <?php endif; ?>


    <div class="content">
    <h1>Photo App</h1>
    Your photos:

    <?php 
    $photos = getAllPhotosforId(getSession('id'));
    foreach($photos as $photo):
    ?>
    <?php $users = getAllUsersToShareWith($photo['id']); ?>

    <h4><?=$photo['name']?></h4>
    <img src="../private/uploads/<?php echo getSession('id').'_'.$photo['name']?>" alt="">
    <?=$photo['caption']?>
    <form action="" method="post">
    <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
    <button type="submit">download</button>
    </form>
    <form action="" method="post">
    
    <label for="share_id">Share with:</label>
    <select name="share_id" id="share_id">
        <?php
            foreach($users as $user){
                if($user['id'] != getSession('id'))
                echo "<option>".$user['id']."</option>";
            }
        ?>
    </select>
    <button type="submit" name="share">Share</button>
    </form>

    <?php endforeach; ?>
    <br />  


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