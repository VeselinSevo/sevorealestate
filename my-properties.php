<?php
session_start();
require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";


if(empty($_SESSION['user'])) {
    header("Location: index.php");
}

require 'public/property/my-properties.phtml';