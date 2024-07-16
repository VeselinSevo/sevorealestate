<?php
function unsetSessionProp($propName) {
    if(isset($_SESSION[$propName])) {
        unset($_SESSION[$propName]);
    }
}
?>
