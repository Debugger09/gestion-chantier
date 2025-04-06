<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

if (!isset($_GET['chantier_id']) || !is_numeric($_GET['chantier_id'])) {
    header('Location: liste.php');
    exit();
}

$chantier_id = intval($_GET['chantier_id']);

// Vérifier que le chantier existe
$stmt = $db->prepare("SELECT id, nom_chantier FROM chantiers WHERE id = ?");
$stmt->execute([$chantier_id]);
$chantier = $stmt->fetch();

if (!$chantier) {
    header('Location: liste.php');
    exit();
}

// Récupérer la liste des matériels disponibles
$materiels = $db->query("SELECT id, nom_materiel FROM materiels ORDER BY nom_materiel")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materiel_id = intval($_POST['materiel_id']);
    $quantite = intval($_POST['quantite']);
    
    if ($materiel_id <= 0 || $quantite <= 0) {
        $errors[] = "Veuillez sélectionner un matériel et entrer une quantité valide";
    } else {
        $stmt = $db->prepare("SELECT id FROM liste_materiel WHERE chantier_id = ? AND materiel_id = ?");
        $stmt->execute([$chantier_id, $materiel_id]);
        
        if ($stmt->fetch()) {
            $errors[] = "Ce matériel est déjà dans la liste. Utilisez la fonction de modification.";
        } else {
            $stmt = $db->prepare("INSERT INTO liste_materiel (chantier_id, materiel_id, quantite_utilisee) VALUES (?, ?, ?)");
            if ($stmt->execute([$chantier_id, $materiel_id, $quantite])) {
                $_SESSION['message'] = "Matériel ajouté avec succès";
                header("Location: details.php?id=$chantier_id&success=1");
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de l'ajout";
            }
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
        <span>←</span> Retour à la liste
</a>
<a href="details.php?id=<?= $chantier_id ?>" class="lien-retour">← Retour aux détails</a>
<div class="ajouter-container">
    <h2>Ajouter du matériel au chantier: <?= htmlspecialchars($chantier['nom_chantier']) ?></h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Matériel</label>
            <select name="materiel_id" required>
                <option value="">-- Sélectionner un matériel --</option>
                <?php foreach ($materiels as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= isset($_POST['materiel_id']) && $_POST['materiel_id'] == $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nom_materiel']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Quantité utilisée</label>
            <input type="number" name="quantite" min="1" value="<?= htmlspecialchars($_POST['quantite'] ?? 1) ?>" required>
        </div>
        
        <button type="submit" class="btn">Ajouter</button>
        <a href="details.php?id=<?= $chantier_id ?>" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>