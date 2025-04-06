<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

if (!isset($_GET['id'])) {
    redirect('liste.php');
}

$id = $_GET['id'];
$errors = [];

// Récupération de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Utilisateur introuvable";
    redirect('liste.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validation
    if (empty($nom)) $errors[] = "Le nom est obligatoire";
    if (empty($email)) $errors[] = "L'email est obligatoire";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    
    // Vérification email unique (sauf pour l'utilisateur actuel)
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) $errors[] = "Cet email est déjà utilisé";
    
    if (empty($errors)) {
        // Mise à jour avec ou sans mot de passe
        if (!empty($password)) {
            if (strlen($password) < 8) {
                $errors[] = "Le mot de passe doit faire au moins 8 caractères";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET nom = ?, email = ?, password = ?, role = ? WHERE id = ?");
                $stmt->execute([$nom, $email, $hashedPassword, $role, $id]);
            }
        } else {
            $stmt = $db->prepare("UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $role, $id]);
        }
        
        if (empty($errors)) {
            $_SESSION['message'] = "Utilisateur modifié avec succès";
            redirect('liste.php?success=1');
        }
    }
}

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/ajouter.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<h2>Modifier l'utilisateur</h2>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">
<a href="liste.php" class="lien-retour">Retour à la liste</a>
    <div class="form-group">
        <label>Nom complet</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>" required>
    </div>
    
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>" required>
    </div>
    
    <div class="form-group">
        <label>Nouveau mot de passe (laisser vide pour ne pas changer)</label>
        <input type="password" name="password">
    </div>
    
    <div class="form-group">
        <label>Rôle</label>
        <select name="role" required>
            <option value="admin" <?= ($_POST['role'] ?? $user['role']) === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            <option value="chef_chantier" <?= ($_POST['role'] ?? $user['role']) === 'chef_chantier' ? 'selected' : '' ?>>Chef de chantier</option>
        </select>
    </div>
    
    <button type="submit" class="btn">Enregistrer</button>
    <a href="liste.php" class="btn">Annuler</a>
</form>

<?php require_once '../../includes/footer.php'; ?>