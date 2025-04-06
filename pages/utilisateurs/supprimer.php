<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$id = $_GET['id'];

// Empêcher la suppression de soi-même
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
    redirect('liste.php');
}

// Vérifier si l'utilisateur existe
$stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    $_SESSION['error'] = "Utilisateur introuvable";
    redirect('liste.php');
}

// Suppression
$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['message'] = "Utilisateur supprimé avec succès";
redirect('liste.php?success=1');
?>