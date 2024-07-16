<?php
session_start();
require "util/flash.php";
require "util/is-logged-in.php";
require 'util/unset-session-prop.php';
require 'public/user/reset-password-start.phtml';

unsetSessionProp("is-password-reset-email-valid");
?>