<?php

$db = new mysqli('localhost','root','','photoapp');

session_start();
require_once 'functions.php';

define('PRIVATE_PATH',__DIR__);
define('PUBLIC_PATH',__DIR__.'../public/');

$errors = [];
