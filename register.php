<?php
session_start();
require "util/flash.php";
require "util/is-logged-in.php";
require "util/is-admin.php";


require 'public/user/register.phtml';
?>