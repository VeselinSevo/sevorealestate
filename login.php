<?php
session_start();
require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";

require 'util/unset-session-prop.php';
require 'public/user/login.phtml';
unsetSessionProp('error');

?>