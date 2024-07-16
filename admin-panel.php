<?php

session_start();
require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";
require 'util/redirect-if-not-admin.php';



$source = $_GET['source'];

if($source == 'show_users') {
    require "services/admin/get-users.php";
}

if($source == 'show_cities') {
    require "services/admin/get-cities.php";
}

if($source == 'show_countries') {
    require "services/admin/get-countries.php";
}

if($source == 'show_messages') {
    require "services/admin/get-messages.php";
}

if(strpos($source, 'edit_country') !== false) {
    require "services/admin/get-country.php";
}

if(strpos($source, 'edit_city') !== false) {
    require "services/admin/get-city.php";
}

if(strpos($source, 'edit_message') !== false) {
    require "services/admin/get-message.php";
}

require "public/admin/admin-panel.phtml";





