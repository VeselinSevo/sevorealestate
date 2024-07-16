<?php
session_start();

error_reporting(error_reporting() & ~E_NOTICE);

require "util/flash.php";
require "util/is-admin.php";
require "util/is-logged-in.php";


if (!empty($_SESSION['user']) && empty($_SESSION['user']['questionnaire_submitted_at'])) {
    require 'services/questionnaire/get-questionnaire.php';
}

require 'public/index.phtml';