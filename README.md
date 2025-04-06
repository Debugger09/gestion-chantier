---  BASE DE DONNES --

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS chantier_db;
USE chantier_db;

-- Table utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'chef_chantier') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table clients
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT
);

-- Table chantiers
CREATE TABLE chantiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_chantier VARCHAR(100) NOT NULL,
    localisation VARCHAR(100) NOT NULL,
    date_debut DATE,
    date_fin DATE,
    id_chef INT,
    client_id INT,
    FOREIGN KEY (id_chef) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

-- Table matériels
CREATE TABLE materiels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_materiel VARCHAR(100) NOT NULL,
    description TEXT,
    quantite_disponible INT DEFAULT 0
);

-- Table de liaison entre chantiers et matériels
CREATE TABLE liste_materiel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chantier_id INT,
    materiel_id INT,
    quantite_utilisee INT NOT NULL,
    FOREIGN KEY (chantier_id) REFERENCES chantiers(id) ON DELETE CASCADE,
    FOREIGN KEY (materiel_id) REFERENCES materiels(id) ON DELETE CASCADE
);

-- Table demandes d'achat
CREATE TABLE demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    chantier_id INT,
    materiel_id INT,
    quantite_demandee INT NOT NULL,
    statut ENUM('en_attente', 'validee', 'refusee') DEFAULT 'en_attente',
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation DATETIME,
    validateur_id INT,
    commentaire TEXT,
    commentaire_validation TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (chantier_id) REFERENCES chantiers(id) ON DELETE CASCADE,
    FOREIGN KEY (materiel_id) REFERENCES materiels(id) ON DELETE CASCADE,
    FOREIGN KEY (validateur_id) REFERENCES users(id)
);


----- INSERE UN UTILISATEUR POUR TE CONNECTER --- NB: SI VOUS CREER VOTRE PROPRE BASE DE DONNES, IL YA LE HASHAGE DU MOT DE PASSE DONC INSERER UN MOT PASSE CRYPTE AVEC LA FONCTION ---EXEMPLE: ---
INSERT INTO users (nom, email, password, role) 
VALUES ('John Doe', 'john.doe@example.com', '$2b$12$G2fqoLegC6GtChTORP2E.u1r0xMu2NQ5UIkXi7pLMS0RbNz1YVyJe', 'admin');

----- NB: J'AI AJOUTE UE UE BASE DE DONNEES IMPORTEE --- LE MOT DE PASSE EST 123456

  
