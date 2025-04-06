<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    
    // Validation
    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    
    // Vérification email unique si fourni
    if (!empty($email)) {
        $stmt = $db->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Cet email est déjà utilisé";
    }
    
    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO clients (nom, email, telephone, adresse) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $telephone, $adresse]);
        
        $success = true;
        $_SESSION['message'] = "Client ajouté avec succès";
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
    <h2>Ajouter un client</h2>
    
    <form method="POST">
    <a href="liste.php" class="lien-retour">Retour à la liste</a>
        <div class="form-group">
            <label>Nom complet</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Adresse</label>
            <textarea name="adresse" rows="3"><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn">Enregistrer</button>
        <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>