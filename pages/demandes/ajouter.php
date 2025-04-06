<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

// Seuls les chefs de chantier peuvent créer des demandes
if (!isChefChantier()) {
    redirect('liste.php');
}

// Récupérer les matériels disponibles
$materiels = $db->query("SELECT id, nom_materiel FROM materiels")->fetchAll();

// Récupérer les chantiers du chef connecté
$stmt = $db->prepare("SELECT id, nom_chantier FROM chantiers WHERE id_chef = ?");
$stmt->execute([$_SESSION['user_id']]);
$chantiers = $stmt->fetchAll();

$errors = [];
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materiel_id = intval($_POST['materiel_id']);
    $chantier_id = intval($_POST['chantier_id']);
    $quantite = intval($_POST['quantite']);
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Validation
    if (empty($materiel_id)) $errors[] = "Matériel obligatoire";
    if (empty($chantier_id)) $errors[] = "Chantier obligatoire";
    if ($quantite <= 0) $errors[] = "Quantité invalide";
    
    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO demandes 
            (user_id, chantier_id, materiel_id, quantite_demandee, commentaire)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $chantier_id,
            $materiel_id,
            $quantite,
            $commentaire
        ]);
        
        $success = true;
        $_SESSION['message'] = "Demande créée avec succès";
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
    <h2>Nouvelle demande</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Matériel *</label>
            <select name="materiel_id" required>
                <option value="">Sélectionnez un matériel</option>
                <?php foreach ($materiels as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= isset($_POST['materiel_id']) && $_POST['materiel_id'] == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom_materiel']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Chantier *</label>
            <select name="chantier_id" required>
                <option value="">Sélectionnez un chantier</option>
                <?php foreach ($chantiers as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= isset($_POST['chantier_id']) && $_POST['chantier_id'] == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom_chantier']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Quantité demandée *</label>
            <input type="number" name="quantite" min="1" value="<?= htmlspecialchars($_POST['quantite'] ?? 1) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Commentaire</label>
            <textarea name="commentaire" rows="3"><?= htmlspecialchars($_POST['commentaire'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn">Envoyer la demande</button>
        <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>