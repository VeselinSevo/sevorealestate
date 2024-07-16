<?php

require "util/flash.php";
require "util/is-logged-in.php";
require "util/is-admin.php";
session_start();
require 'public/user/contact.phtml';