<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$chantier_id = $_GET['id'];
$errors = [];

// Récupération du chantier
$stmt = $db->prepare("SELECT c.*, cl.nom as client_nom FROM chantiers c JOIN clients cl ON c.client_id = cl.id WHERE c.id = ?");
$stmt->execute([$chantier_id]);
$chantier = $stmt->fetch();

if (!$chantier) {
    $_SESSION['error'] = "Chantier introuvable";
    redirect('liste.php');
}

// Récupération des chefs disponibles
$chefs = $db->query("SELECT id, nom FROM users WHERE role = 'chef_chantier' ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chef_id = $_POST['chef_id'] ?: null;
    
    if (empty($chef_id)) {
        $errors[] = "Veuillez sélectionner un chef de projet";
    } else {
        $stmt = $db->prepare("UPDATE chantiers SET id_chef = ? WHERE id = ?");
        $stmt->execute([$chef_id, $chantier_id]);
        
        $_SESSION['message'] = "Chef de projet attribué avec succès";
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
    <h2>Attribuer un chef de projet</h2>
    
    <div class="form-group">
        <label>Chantier</label>
        <input type="text" value="<?= htmlspecialchars($chantier['nom_chantier']) ?>" readonly>
    </div>
    
    <div class="form-group">
        <label>Client</label>
        <input type="text" value="<?= htmlspecialchars($chantier['client_nom']) ?>" readonly>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Chef de projet</label>
            <select name="chef_id" required>
                <option value="">Sélectionnez un chef</option>
                <?php foreach ($chefs as $chef): ?>
                    <option value="<?= $chef['id'] ?>"><?= htmlspecialchars($chef['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn">Attribuer</button>
        <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>