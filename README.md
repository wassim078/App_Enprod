# 🌱 LiveCycle - Plateforme de Recyclage (Version Symfony)

![Symfony](https://img.shields.io/badge/Symfony-6.3-%23000000?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2-%23777BB4?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-%234479A1?logo=mysql)

**Projet développé à [Esprit School of Engineering](https://www.esprit.tn/) - Année universitaire 2024/2025**

---

## 📝 Description du Projet
**LiveCycle** est une plateforme web de recyclage qui connecte :
- � **Particuliers** (vendeurs de produits recyclables)  
- 🏭 **Entreprises de recyclage**  
- ♻️ **Collecteurs** (gestion de points de collecte)

**Objectifs ODD** :
- ✅ Réduction des déchets via la réutilisation  
- 🌍 Création d'emplois verts  
- 📚 Sensibilisation à l'économie circulaire

---

## 📋 Table des Matières
1. [Installation](#📥-installation)
2. [Fonctionnalités](#✨-fonctionnalités)
3. [Structure du Projet](#🏗️-structure-du-projet)
4. [Technologies](#🛠️-technologies-utilisées)
5. [APIs](#🔌-apis-intégrées)
6. [Contribution](#👥-contribution)
7. [Licence](#📄-licence)

---

## 📥 Installation
### Prérequis
- PHP 8.2+
- Composer 2.6+
- Symfony CLI 6.3+
- MySQL 8.0+

### Étapes d'installation
```bash
# Cloner le dépôt
git clone https://github.com/votre-utilisateur/livecycle-symfony.git
cd livecycle-symfony

# Installer les dépendances
composer install

# Configurer la base de données (fichier .env)
DATABASE_URL="mysql://root:@127.0.0.1:3306/livecycle?serverVersion=8.0"

# Créer la base de données et appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Lancer le serveur
symfony server:start
```
## ✨ Fonctionnalités

### 👤 Module Utilisateur

🔐 Authentification JWT

📧 Réinitialisation de mot de passe par email

👤 Gestion des profils utilisateurs

### 📢 Module Annonces
🖼️ Publication d'annonces avec photos

🔍 Filtrage par catégories (métal, plastique, verre)

🔔 Notifications SMS/Email (Twilio)

### 💬 Module Forum
📝 Création de posts thématiques

💬 Système de commentaires

📥 Export PDF des discussions (DomPDF)

### 🚛 Module Commandes
💳 Paiement sécurisé (Stripe)

📦 Suivi des livraisons en temps réel

📄 Génération automatique de factures
