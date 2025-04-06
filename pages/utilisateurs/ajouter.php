<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validation
    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (empty($email)) $errors[] = "L'email est obligatoire";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (empty($password)) $errors[] = "Le mot de passe est obligatoire";
    if (strlen($password) < 8) $errors[] = "Le mot de passe doit faire au moins 8 caractères";
    
    // Vérification email unique
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Cet email est déjà utilisé";
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $email, $hashedPassword, $role]);
        
        $success = true;
        $_SESSION['message'] = "Utilisateur ajouté avec succès";
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
    <h2>Ajouter un utilisateur</h2>
    
    <form method="POST">
    <div class="form-group">
        <label>Nom complet</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="password" required>
    </div>
    
    <div class="form-group">
        <label>Rôle</label>
        <select name="role" required>
            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            <option value="chef_chantier" <?= ($_POST['role'] ?? '') === 'chef_chantier' ? 'selected' : '' ?>>Chef de chantier</option>
        </select>
    </div>
    
    <button type="submit" class="btn">Enregistrer</button>
    <a href="liste.php" class="btn">Annuler</a>
    </form>
</div>


<?php require_once '../../includes/footer.php'; ?>