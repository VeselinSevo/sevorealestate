<?php

if(!isAdmin()) {
    header("Location: login.php");
    exit;
}