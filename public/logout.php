<?php
require_once '../private/init.php';
session_destroy();

redirect_to('index.php');

?>