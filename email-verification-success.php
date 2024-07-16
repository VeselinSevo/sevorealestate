<?php
session_start();
require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";


require 'public/user/email-verification-success.phtml';
