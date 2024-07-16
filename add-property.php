<?php

session_start();
require "util/flash.php";
require "util/is-logged-in.php";
require "util/is-admin.php";
if(empty($_SESSION['user'])) {
    header("Location: index.php");
}

require 'public/property/add-property.phtml';