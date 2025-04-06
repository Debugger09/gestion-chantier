<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$id = $_GET['id'];

// Vérifier si le client existe
$stmt = $db->prepare("SELECT id FROM clients WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    $_SESSION['error'] = "Client introuvable";
    redirect('liste.php');
}

// Vérifier si le client est utilisé dans des chantiers
$stmt = $db->prepare("SELECT id FROM chantiers WHERE client_id = ? LIMIT 1");
$stmt->execute([$id]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Impossible de supprimer : ce client est lié à des chantiers";
    redirect('liste.php');
}

// Suppression
$stmt = $db->prepare("DELETE FROM clients WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['message'] = "Client supprimé avec succès";
redirect('liste.php?success=1');
?>