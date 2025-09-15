# Système de Notifications Email - Parasel-Bio

## 📧 Fonctionnalité
Quand l'admin met à jour le statut d'une commande, le client concerné reçoit automatiquement un email de notification.

## ⚙️ Configuration

### 1. Configuration SMTP
Ajoutez ces lignes à votre fichier `.env` :

```env
# Configuration Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="Parasel-Bio"
```

### 2. Configuration Gmail
1. Activez l'authentification à 2 facteurs sur votre compte Gmail
2. Générez un "mot de passe d'application" :
   - Allez dans Paramètres Google > Sécurité
   - Activez la validation en 2 étapes
   - Générez un mot de passe d'application
   - Utilisez ce mot de passe dans `MAIL_PASSWORD`

### 3. Autres fournisseurs
- **Outlook/Hotmail** : `smtp-mail.outlook.com:587`
- **Yahoo** : `smtp.mail.yahoo.com:587`

## 🧪 Test

### Tester l'envoi d'email
```bash
php artisan test:email votre-email@example.com
```

### Vider le cache
```bash
php artisan config:clear
php artisan cache:clear
```

## 📋 Fonctionnement

### 1. Mise à jour du statut
Quand l'admin change le statut d'une commande :
- **En cours** → **Livrée**
- **En cours** → **Annulée**
- **Livrée** → **Annulée** (si nécessaire)

### 2. Notification automatique
- Email envoyé au client concerné
- Contient les détails de la commande
- Lien vers "Mes commandes"
- Design professionnel avec le logo Parasel-Bio

### 3. Contenu de l'email
- Nom du client
- Numéro de commande
- Ancien et nouveau statut
- Date de la commande
- Montant total
- Méthode de paiement
- Lien vers l'espace client

## 🎨 Personnalisation

### Modifier le design de l'email
Éditez le fichier : `resources/views/emails/order-status-updated.blade.php`

### Modifier le contenu
Éditez le fichier : `app/Notifications/OrderStatusUpdated.php`

## 🔧 Dépannage

### Email non reçu
1. Vérifiez la configuration SMTP
2. Vérifiez les logs : `storage/logs/laravel.log`
3. Testez avec : `php artisan test:email votre-email@example.com`

### Erreur SMTP
1. Vérifiez les identifiants
2. Vérifiez le port (587 pour TLS, 465 pour SSL)
3. Vérifiez que l'authentification à 2 facteurs est activée

## 📁 Fichiers concernés
- `app/Notifications/OrderStatusUpdated.php` - Classe de notification
- `app/Http/Controllers/Admin/OrderController.php` - Envoi de la notification
- `resources/views/emails/order-status-updated.blade.php` - Template email
- `app/Console/Commands/TestEmailNotification.php` - Commande de test
