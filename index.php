<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireAuth();

require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/dashboard.css">
<div class="dashboard-container">
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></h2>
    
    <div class="dashboard-grid">
        <?php if (isAdmin()): ?>
            <!-- Carte Admin 1: Statistiques globales -->
            <div class="dashboard-card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Statistiques globales</h3>
                </div>
                <div class="card-body">
                    <?php
                    // Utilisation de try-catch pour gérer les erreurs
                    try {
                        $users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
                        $chantiers = $db->query("SELECT COUNT(*) FROM chantiers")->fetchColumn();
                        $materiels = $db->query("SELECT COUNT(*) FROM materiels")->fetchColumn();
                    } catch (PDOException $e) {
                        $users = $chantiers = $materiels = 'N/A';
                        error_log("Erreur DB: " . $e->getMessage());
                    }
                    ?>
                    <div class="stat-item">
                        <span class="stat-value"><?= $users ?></span>
                        <span class="stat-label">Utilisateurs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= $chantiers ?></span>
                        <span class="stat-label">Chantiers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= $materiels ?></span>
                        <span class="stat-label">Matériels</span>
                    </div>
                </div>
            </div>

            <!-- Carte Admin 2: Demandes en attente -->
            <div class="dashboard-card alert-card">
                <div class="card-header">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Demandes en attente</h3>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $db->prepare("SELECT COUNT(*) FROM demandes WHERE statut = 'en_attente'");
                        $stmt->execute();
                        $demandes = $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        $demandes = 'N/A';
                        error_log("Erreur DB: " . $e->getMessage());
                    }
                    ?>
                    <span class="badge"><?= $demandes ?> nouvelles</span>
                    <p>Demandes nécessitant votre validation</p>
                </div>
                <div class="card-footer">
                    <a href="pages/demandes/liste.php?filter=en_attente" class="btn btn-warning">Traiter</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Carte commune: Mes chantiers -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-hard-hat"></i>
                <h3>Mes chantiers</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    if (isAdmin()) {
                        $stmt = $db->query("SELECT COUNT(*) FROM chantiers WHERE date_fin >= CURDATE()");
                    } else {
                        $stmt = $db->prepare("SELECT COUNT(*) FROM chantiers WHERE id_chef = ? AND date_fin >= CURDATE()");
                        $stmt->execute([$_SESSION['user_id']]);
                    }
                    $count = $stmt ? $stmt->fetchColumn() : 0;
                } catch (PDOException $e) {
                    $count = 'N/A';
                    error_log("Erreur DB: " . $e->getMessage());
                }
                ?>
                <span class="stat-value"><?= $count ?></span>
                <p>Chantiers actifs</p>
                
                <?php if ($count > 0 && $count !== 'N/A'): ?>
                <ul class="chantier-list">
                    <?php
                    try {
                        if (isAdmin()) {
                            $stmt = $db->query("SELECT id, nom_chantier FROM chantiers WHERE date_fin >= CURDATE() ORDER BY date_debut LIMIT 3");
                        } else {
                            $stmt = $db->prepare("SELECT id, nom_chantier FROM chantiers WHERE id_chef = ? AND date_fin >= CURDATE() ORDER BY date_debut LIMIT 3");
                            $stmt->execute([$_SESSION['user_id']]);
                        }
                        
                        while ($row = $stmt->fetch()):
                    ?>
                    <li>
                        <a href="pages/chantiers/details.php?id=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['nom_chantier']) ?>
                        </a>
                    </li>
                    <?php 
                        endwhile;
                    } catch (PDOException $e) {
                        echo '<li>Erreur de chargement</li>';
                        error_log("Erreur DB: " . $e->getMessage());
                    }
                    ?>
                </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <a href="pages/chantiers/liste.php" class="btn btn-primary">Voir tous</a>
                <?php if (isAdmin()): ?>
                    <a href="pages/chantiers/ajouter.php" class="btn btn-success">Nouveau</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Carte commune: Matériels -->
        <div class="dashboard-card">
            <div class="card-header">
                <i class="fas fa-tools"></i>
                <h3>Matériels</h3>
            </div>
            <div class="card-body">
                <?php
                try {
                    $total = $db->query("SELECT SUM(quantite_disponible) FROM materiels")->fetchColumn();
                    $dispo = $db->query("SELECT COUNT(*) FROM materiels WHERE quantite_disponible > 0")->fetchColumn();
                } catch (PDOException $e) {
                    $total = $dispo = 'N/A';
                    error_log("Erreur DB: " . $e->getMessage());
                }
                ?>
                <div class="stat-item">
                    <span class="stat-value"><?= $total ?></span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $dispo ?></span>
                    <span class="stat-label">Disponibles</span>
                </div>
            </div>
            <div class="card-footer">
                <a href="pages/materiels/liste.php" class="btn btn-primary">Inventaire</a>
                <a href="pages/materiels/ajouter.php" class="btn btn-success">Ajouter</a>
                
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>