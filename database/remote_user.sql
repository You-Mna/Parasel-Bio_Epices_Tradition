-- Crée un utilisateur distant pour l'application Laravel
-- À lancer sur le serveur MySQL (adapter le mot de passe si nécessaire)

CREATE USER IF NOT EXISTS 'parasel_user'@'%' IDENTIFIED BY 'motdepasse_solide';
GRANT ALL PRIVILEGES ON parasel_bio.* TO 'parasel_user'@'%';
FLUSH PRIVILEGES;



