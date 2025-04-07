<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// Récupération des clients et chefs de projet
$clients = $db->query("SELECT id, nom FROM clients ORDER BY nom")->fetchAll();
$chefs = $db->query("SELECT id, nom FROM users WHERE role = 'chef_chantier' ORDER BY nom")->fetchAll();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_chantier = trim($_POST['nom_chantier']);
    $localisation = trim($_POST['localisation']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'] ?: null;
    $client_id = $_POST['client_id'];
    $id_chef = $_POST['id_chef'] ?: null;
    
    // Validation
    if (empty($nom_chantier)) $errors[] = "Le nom du chantier est obligatoire";
    if (empty($localisation)) $errors[] = "La localisation est obligatoire";
    if (empty($client_id)) $errors[] = "Le client est obligatoire";
    if ($date_fin && $date_fin < $date_debut) $errors[] = "La date de fin doit être après la date de début";
    
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO chantiers (nom_chantier, localisation, date_debut, date_fin, client_id, id_chef) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom_chantier, $localisation, $date_debut, $date_fin, $client_id, $id_chef]);
        
        $success = true;
        $_SESSION['message'] = "Chantier ajouté avec succès";
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
    <h2>Ajouter un chantier</h2>
    
    <form method="POST">
    <a href="liste.php" class="lien-retour">Retour à la liste</a>
        <div class="form-group">
            <label>Nom du chantier</label>
            <input type="text" name="nom_chantier" value="<?= htmlspecialchars($_POST['nom_chantier'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Localisation</label>
            <input type="text" name="localisation" value="<?= htmlspecialchars($_POST['localisation'] ?? '') ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Date de début</label>
                <input type="date" name="date_debut" value="<?= htmlspecialchars($_POST['date_debut'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Date de fin (optionnel)</label>
                <input type="date" name="date_fin" value="<?= htmlspecialchars($_POST['date_fin'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Client</label>
            <select name="client_id" required>
                <option value="">Sélectionnez un client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['id'] ?>" <?= ($_POST['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($client['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Chef de projet (optionnel)</label>
            <select name="id_chef">
                <option value="">Non attribué</option>
                <?php foreach ($chefs as $chef): ?>
                    <option value="<?= $chef['id'] ?>" <?= ($_POST['id_chef'] ?? '') == $chef['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($chef['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn">Enregistrer</button>
        <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>