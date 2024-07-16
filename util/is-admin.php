<?php
session_start();
function isAdmin() {
    return (isset($_SESSION["user"]) && $_SESSION["role_id"] == 2);
}