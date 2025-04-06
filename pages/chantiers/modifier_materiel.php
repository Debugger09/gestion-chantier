<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit();
}

$id = intval($_GET['id']);

$query = "SELECT lm.*, m.nom_materiel, c.nom_chantier 
          FROM liste_materiel lm
          JOIN materiels m ON lm.materiel_id = m.id
          JOIN chantiers c ON lm.chantier_id = c.id
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
    $quantite = intval($_POST['quantite']);
    
    if ($quantite <= 0) {
        $errors[] = "La quantité doit être supérieure à 0";
    } else {
        $stmt = $db->prepare("UPDATE liste_materiel SET quantite_utilisee = ? WHERE id = ?");
        if ($stmt->execute([$quantite, $id])) {
            $_SESSION['message'] = "Quantité modifiée avec succès";
            header("Location: details.php?id=" . $materiel['chantier_id'] . "&success=1");
            exit();
        } else {
            $errors[] = "Une erreur est survenue lors de la modification";
        }
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

<a href="details.php?id=<?= $chantier_id ?>" class="back-btn">
    <span>←</span> Retour aux details
</a>
<div class="ajouter-container">
    <h2>Modifier la quantité pour: <?= htmlspecialchars($materiel['nom_materiel']) ?></h2>
    <p class="info-chantier">Chantier: <?= htmlspecialchars($materiel['nom_chantier']) ?></p>
    
    <form method="POST">
        <div class="form-group">
            <label>Quantité utilisée</label>
            <input type="number" name="quantite" min="1" 
                   value="<?= htmlspecialchars($materiel['quantite_utilisee']) ?>" required>
        </div>
        
        <button type="submit" class="btn">Enregistrer</button>
        <a href="details.php?id=<?= $materiel['chantier_id'] ?>" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>