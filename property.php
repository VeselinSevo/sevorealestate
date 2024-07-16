<?php
session_start();
require "util/is-logged-in.php";
require "util/is-admin.php";



$property = null;

require 'services/property/get-property.php';

require 'public/property/property.phtml';