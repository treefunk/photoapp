<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../private/includes/header.php'; ?>
<?php require_once '../private/init.php'; ?>

<?php // process $_POST
    if(isset($_POST['share'])){
        sharePhotoTo($_POST['share_id'],$_POST['photo_id']);
    }else if(isset($_POST['download'])){
        downloadPhoto($_POST['photo_id']);
    }


?>





<body>

    <?php if(someoneIsLoggedIn()): ?>
        Hi <?=getSession('username');?> !!
    <?php else: redirect_to('login.php'); ?>
    <?php endif; ?>


    <div class="content">
    


    <?php 
    $photos = getAllPhotosforId(getSession('id'));
    if($photos):
    foreach($photos as $photo):
    ?>
    <?php $users = getAllUsersToShareWith($photo['id']);
    ?>

    <h4><?=$photo['name']?></h4>
    <img src="../private/uploads/<?php echo getSession('id').'_'.$photo['name']?>" alt="">
    <?=$photo['caption']?>
    <form action="" method="post">
    <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
    <button type="submit" name="download">download</button>
    </form>
    <?php if(!empty($users)): ?>
    <form action="" method="post">
    <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
    <label for="share_id">Share with:</label>
    <select name="share_id" id="share_id">
        <?php
                foreach($users as $id => $val){
                    echo "<option value=".$id.">".$val."</option>";
                }
        ?>
    </select>
    <button type="submit" name="share">Share</button>
    </form>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php else: ?>
    <h2>You haven't uploaded any photos yet!</h2>
    <?php endif; ?>
    <br />  
    
    <h3>photos shared with you:</h3>
     <br />
    <?php 
    $photosShared = photosSharedWithThisID(getSession('id'));
    ?>

    <?php if($photosShared): ?>
    <?php foreach($photosShared as $photo): ?>
            <h4><?=$photo['name']?></h4>
            <img src="<?php echo "../private/uploads/{$photo['uploaded_by']}_{$photo['name']}"; ?>" alt="">
            <br />
            Shared by: <?php echo photoIdToUsername($photo['uploaded_by']); ?> <br />
    <?php endforeach; ?>
    <?php else: ?>
    <?php endif; ?>

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