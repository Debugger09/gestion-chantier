<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Récupération des utilisateurs
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/liste.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="user-management-container">
    <h2>Gestion des utilisateurs</h2>
    <a href="ajouter.php" class="add-user-btn">Ajouter un utilisateur</a>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Opération effectuée avec succès</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><span class="role-badge <?= $user['role'] ?>"><?= $user['role'] ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="modifier.php?id=<?= $user['id'] ?>" class="btn edit-btn">Modifier</a>
                            <a href="supprimer.php?id=<?= $user['id'] ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>