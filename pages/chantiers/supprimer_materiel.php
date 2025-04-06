<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit();
}

$id = intval($_GET['id']);

$query = "SELECT lm.*, c.nom_chantier, m.nom_materiel 
          FROM liste_materiel lm
          JOIN chantiers c ON lm.chantier_id = c.id
          JOIN materiels m ON lm.materiel_id = m.id
          WHERE lm.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$materiel = $stmt->fetch();

if (!$materiel) {
    header('Location: liste.php');
    exit();
}

// Vérifier les permissions
if (!isAdmin()) {
    $stmt = $db->prepare("SELECT id_chef FROM chantiers WHERE id = ?");
    $stmt->execute([$materiel['chantier_id']]);
    $chantier = $stmt->fetch();
    
    if ($_SESSION['user_id'] != $chantier['id_chef']) {
        header('Location: details.php?id=' . $materiel['chantier_id']);
        exit();
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("DELETE FROM liste_materiel WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['message'] = "Matériel supprimé du chantier";
        header("Location: details.php?id=" . $materiel['chantier_id'] . "&success=1");
        exit();
    } else {
        $errors[] = "Une erreur est survenue lors de la suppression";
    }
}

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/ajouter.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<a href="details.php?id=<?= $materiel['chantier_id'] ?>" class="lien-retour">← Retour aux détails</a>
<div class="ajouter-container">
    <h2>Supprimer le matériel du chantier</h2>
    
    <div class="info-suppression">
        <p><strong>Matériel:</strong> <?= htmlspecialchars($materiel['nom_materiel']) ?></p>
        <p><strong>Quantité utilisée:</strong> <?= $materiel['quantite_utilisee'] ?></p>
        <p><strong>Chantier:</strong> <?= htmlspecialchars($materiel['nom_chantier']) ?></p>
    </div>
    
    <div class="alert warning">
        <p>Cette action est irréversible. Êtes-vous sûr de vouloir supprimer ce matériel du chantier ?</p>
    </div>
    
    <form method="POST">
        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
        <a href="details.php?id=<?= $materiel['chantier_id'] ?>" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>