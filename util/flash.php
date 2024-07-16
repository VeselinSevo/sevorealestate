<?php
function setFlashMessage($key, $message) {
    $_SESSION[$key] = $message;
}

function getFlashMessage($key) {
    if (isset($_SESSION[$key])) {
    $message = $_SESSION[$key];
    unset($_SESSION[$key]); // Remove the message to prevent it from being displayed again
    return $message;
    }
return null;
}