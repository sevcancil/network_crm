<?php
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../pages/login.php");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>