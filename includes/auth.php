<?php
// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier les rôles
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

function isChefChantier() {
    return isLoggedIn() && $_SESSION['role'] === 'chef_chantier';
}

// Protection de page
function requireAuth() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        die("Accès refusé : vous n'avez pas les droits nécessaires");
    }
}
?>