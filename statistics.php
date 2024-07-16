<?php
session_start();
require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";
require 'util/redirect-if-not-admin.php';


if (!isset($_SESSION["user"]) || $_SESSION["role_id"] != 2) {
    // Redirect the user to the login page or display an error message
    // For example:
    header("Location: login.php");
    exit;
}

require 'services/admin/get-statistics.php';
require 'public/admin/statistics.phtml';