<?php

$db = new mysqli('localhost','root','','photoapp');

require_once 'functions.php';

define('PRIVATE_PATH',__DIR__);

$errors = [];
