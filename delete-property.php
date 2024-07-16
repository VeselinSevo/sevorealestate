<?php
require "util/flash.php";
require "util/is-logged-in.php";
session_start();

if(empty($_SESSION['user'])) {
    header("Location: index.php");
}

require 'services/property/delete-property.phtml';

