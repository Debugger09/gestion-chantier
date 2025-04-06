<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$id = $_GET['id'];

// Vérifier si le matériel existe
$stmt = $db->prepare("SELECT id FROM materiels WHERE id = ?");
$stmt->execute([$id]);

if (!$stmt->fetch()) {
    $_SESSION['error'] = "Matériel introuvable";
    redirect('liste.php');
}

// Vérifier si le matériel est utilisé dans des chantiers
$stmt = $db->prepare("SELECT id FROM liste_materiel WHERE materiel_id = ? LIMIT 1");
$stmt->execute([$id]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Impossible de supprimer : matériel utilisé dans des chantiers";
    redirect('liste.php');
}

// Suppression
$stmt = $db->prepare("DELETE FROM materiels WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['message'] = "Matériel supprimé avec succès";
redirect('liste.php?success=1');
?>