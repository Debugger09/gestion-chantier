<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit();
}

$id = intval($_GET['id']);

// Récupérer les informations du matériel
$stmt = $db->prepare("SELECT * FROM materiels WHERE id = ?");
$stmt->execute([$id]);
$materiel = $stmt->fetch();

if (!$materiel) {
    $_SESSION['error'] = "Matériel introuvable";
    header('Location: liste.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_materiel = trim($_POST['nom_materiel']);
    $description = trim($_POST['description']);
    $quantite = intval($_POST['quantite_disponible']);
    
    // Validation
    if (empty($nom_materiel)) $errors[] = "Le nom du matériel est obligatoire";
    if ($quantite < 0) $errors[] = "La quantité doit être positive";
    
    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE materiels SET nom_materiel = ?, description = ?, quantite_disponible = ? WHERE id = ?");
        if ($stmt->execute([$nom_materiel, $description, $quantite, $id])) {
            $_SESSION['message'] = "Matériel modifié avec succès";
            header('Location: liste.php?success=1');
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

<div class="ajouter-container">
    <a href="liste.php" class="lien-retour">Retour à la liste</a>
    
    <h2>Modifier le matériel</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Nom du matériel</label>
            <input type="text" name="nom_materiel" value="<?= htmlspecialchars($materiel['nom_materiel']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($materiel['description']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Quantité disponible</label>
            <input type="number" name="quantite_disponible" min="0" value="<?= $materiel['quantite_disponible'] ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="liste.php" class="btn cancel">Annuler</a>
        </div>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>