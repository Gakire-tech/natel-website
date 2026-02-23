# Guide de Configuration du Serveur Email

Ce guide explique comment configurer le serveur email pour l'envoi d'emails de confirmation aux utilisateurs.

## 1. Configuration via l'interface d'administration

Le système permet de configurer les paramètres SMTP directement depuis l'interface d'administration du site, dans la section "Paramètres".

### Paramètres de configuration accessibles :

- `SMTP Host` : Adresse du serveur SMTP (ex: smtp.gmail.com)
- `SMTP Port` : Port du serveur SMTP (587 pour TLS, 465 pour SSL)
- `SMTP Username` : Adresse email pour l'authentification
- `SMTP Password` : Mot de passe ou mot de passe d'application
- `SMTP Encryption` : Méthode de chiffrement ('tls', 'ssl' ou 'none')
- `Email Enabled` : Activation/désactivation du système d'email
- `Email Sender Address` : Adresse email de l'expéditeur
- `Email Sender Name` : Nom de l'expéditeur

## 2. Configuration pour Gmail

Si vous utilisez un compte Gmail, suivez ces étapes :

1. Activez l'authentification à 2 facteurs sur votre compte Google
2. Générez un mot de passe d'application :
   - Allez dans votre compte Google
   - Sélectionnez "Sécurité"
   - Sous "Accès à votre compte Google", sélectionnez "Mots de passe d'application"
   - Générez un mot de passe d'application pour "Mail"
3. Utilisez ce mot de passe dans le paramètre `SMTP Password`

Paramètres recommandés pour Gmail :
- `SMTP Host` : smtp.gmail.com
- `SMTP Port` : 587
- `SMTP Encryption` : TLS

## 3. Configuration pour d'autres fournisseurs

### Outlook/Hotmail :
- `SMTP Host` : smtp-mail.outlook.com
- `SMTP Port` : 587
- `SMTP Encryption` : TLS

### Yahoo :
- `SMTP Host` : smtp.mail.yahoo.com
- `SMTP Port` : 587
- `SMTP Encryption` : TLS

## 4. Personnalisation du message

Le message de confirmation envoyé aux utilisateurs peut être personnalisé dans l'interface d'administration du site :

1. Connectez-vous à l'interface d'administration
2. Allez dans la section "Paramètres"
3. Modifiez le champ "Confirmation Message" avec le texte souhaité
4. Enregistrez les modifications

Le message personnalisé sera utilisé dans tous les emails de confirmation envoyés après soumission de formulaires de contact ou de demandes de devis.

## 5. Informations incluses dans l'email

Les emails de confirmation contiennent automatiquement :
- Le nom de la personne à qui l'email est envoyé
- Le message personnalisé défini dans les paramètres
- Des informations supplémentaires spécifiques à la demande :
  - Pour les formulaires de contact : sujet et date
  - Pour les demandes de devis : type de projet, fourchette budgétaire, délai, etc.
- La signature avec le nom du site

## 6. Dépannage

Si les emails ne sont pas envoyés :

1. Vérifiez que les paramètres SMTP sont corrects dans l'interface d'administration
2. Assurez-vous que le pare-feu n'empêche pas les connexions sortantes
3. Consultez les logs d'erreur dans votre serveur pour plus de détails
4. Pour Gmail, assurez-vous que les mots de passe d'application sont activés