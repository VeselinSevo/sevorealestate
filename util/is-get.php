<?php
function is_get() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}
?>