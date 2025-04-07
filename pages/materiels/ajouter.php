<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_materiel = trim($_POST['nom_materiel']);
    $description = trim($_POST['description']);
    $quantite = $_POST['quantite_disponible'];
    
    // Validation
    if (empty($nom_materiel)) $errors[] = "Le nom du matériel est obligatoire";
    if (!is_numeric($quantite) || $quantite < 0) $errors[] = "Quantité invalide";
    
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO materiels (nom_materiel, description, quantite_disponible) VALUES (?, ?, ?)");
        $stmt->execute([$nom_materiel, $description, $quantite]);
        
        $success = true;
        $_SESSION['message'] = "Matériel ajouté avec succès";
        redirect('liste.php?success=1');
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

<a href="liste.php" class="lien-retour">← Retour à la liste</a>
<div class="ajouter-container">
    <h2>Ajouter un matériel</h2>
    
    <form method="POST">
    <a href="liste.php" class="lien-retour">Retour à la liste</a>
        <div class="form-group">
            <label>Nom du matériel</label>
            <input type="text" name="nom_materiel" value="<?= htmlspecialchars($_POST['nom_materiel'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Quantité disponible</label>
            <input type="number" name="quantite_disponible" value="<?= htmlspecialchars($_POST['quantite_disponible'] ?? 0) ?>" min="0" required>
        </div>
        
        <button type="submit" class="btn">Enregistrer</button>
        <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>