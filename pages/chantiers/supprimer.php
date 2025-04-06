<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$id = $_GET['id'];

// Vérifier si le chantier existe
$stmt = $db->prepare("SELECT id FROM chantiers WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    $_SESSION['error'] = "Chantier introuvable";
    redirect('liste.php');
}

// Vérifier si le chantier a du matériel attribué
$stmt = $db->prepare("SELECT id FROM liste_materiel WHERE chantier_id = ? LIMIT 1");
$stmt->execute([$id]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Impossible de supprimer : matériel encore attribué";
    redirect('liste.php');
}

// Vérifier si le chantier a des demandes
$stmt = $db->prepare("SELECT id FROM demandes WHERE chantier_id = ? LIMIT 1");
$stmt->execute([$id]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Impossible de supprimer : demandes existantes";
    redirect('liste.php');
}

// Suppression
$stmt = $db->prepare("DELETE FROM chantiers WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['message'] = "Chantier supprimé avec succès";
redirect('liste.php?success=1');
?>