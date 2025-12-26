# Solution Alternative - Sans Changer le Document Root

## 🎯 Situation

Vous ne trouvez pas l'option "Document Root" dans votre panneau Hostinger. C'est normal sur certains plans Hostinger.

## ✅ Solution : Déplacer le Contenu du Dossier Public

Puisque le document root est fixé sur `public_html` et ne peut pas être changé, nous allons déplacer le contenu du dossier `public` vers la racine.

---

## 📋 Étapes à Suivre

### 1. Connexion à Hostinger

Connectez-vous au **Gestionnaire de fichiers** Hostinger :
1. Panneau Hostinger → **Fichiers** → **Gestionnaire de fichiers**
2. Naviguez vers `/domains/vitalbridge.kgslab.com/public_html/`

---

### 2. Déplacer les Fichiers

**A. Déplacer le contenu de `public/` vers la racine** :

Dans le gestionnaire de fichiers :

1. Ouvrez le dossier `public/`
2. **Sélectionnez TOUS les fichiers** dans le dossier `public/` :
   - `index.php`
   - `.htaccess`
   - `favicon.ico`
   - Tous les dossiers (assets, etc.)

3. **Déplacez-les** (bouton "Move" ou "Déplacer")vers :
   ```
   /domains/vitalbridge.kgslab.com/public_html/
   ```

4. **Confirmez le remplacement** si demandé

**Résultat attendu** : Les fichiers qui étaient dans `public/` sont maintenant à la racine de `public_html/`

---

### 3. Modifier le fichier index.php

Le fichier `index.php` contient des chemins vers les autres dossiers Laravel. Nous devons les ajuster.

**A. Ouvrez** le fichier `/public_html/index.php` dans l'éditeur

**B. Trouvez** ces lignes (vers la ligne 14) :
```php
require __DIR__.'/../vendor/autoload.php';
```

et (vers la ligne 18) :
```php
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**C. Remplacez-les** par :
```php
require __DIR__.'/vendor/autoload.php';
```

et :
```php
$app = require_once __DIR__.'/bootstrap/app.php';
```

**Explication** : On enlève `/../` car les dossiers `vendor` et `bootstrap` sont maintenant au même niveau que `index.php`.

---

### 4. Supprimer le Dossier public/ Vide (Optionnel)

Après avoir déplacé tous les fichiers, le dossier `public/` devrait être vide. Vous pouvez le supprimer.

---

### 5. Vérifier le Fichier .htaccess à la Racine

**A. Supprimez** le fichier `.htaccess` que nous avions créé précédemment à la racine (celui qui redirige vers `public/`)

**B. Assurez-vous** qu'il y a maintenant un fichier `.htaccess` à la racine avec ce contenu (celui qui vient de `public/`) :

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ⚠️ IMPORTANT : Protéger les Dossiers Sensibles

Maintenant que tous les dossiers sont accessibles depuis le web, vous devez **protéger** les dossiers sensibles.

### Créer des Fichiers .htaccess de Protection

Créez un fichier `.htaccess` dans chacun de ces dossiers :
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `routes/`
- `storage/`
- `vendor/`

**Contenu à mettre dans chaque `.htaccess`** :
```apache
# Deny all access
Order allow,deny
Deny from all
```

Cela empêchera l'accès web direct à ces dossiers.

---

## 🧪 Test Final

Après toutes ces modifications :

```bash
curl -X POST https://vitalbridge.kgslab.com/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"phone":"70477652", "first_name":"Test User", "last_name":"Test User", "email":"test@example.com"}'
```

**Résultat attendu** :
- `HTTP/2 200` ou `HTTP/2 422` (validation)
- Réponse JSON de Laravel

---

## 📊 Structure Finale

```
/public_html/
├── .htaccess                    ← Fichier Laravel .htaccess
├── index.php                    ← Modifié pour pointer vers ./
├── favicon.ico
├── assets/
├── app/
│   └── .htaccess               ← Protection "Deny from all"
├── bootstrap/
│   └── .htaccess               ← Protection
├── config/
│   └── .htaccess               ← Protection
├── database/
│   └── .htaccess               ← Protection
├── routes/
│   └── .htaccess               ← Protection
├── storage/
│   └── .htaccess               ← Protection
├── vendor/
│   └── .htaccess               ← Protection
└── ...
```

---

## ⚠️ Note Sur la Sécurité

Cette solution est **moins sécurisée** que d'avoir le document root pointant vers `public/`, car les dossiers du framework sont potentiellement accessibles. C'est pourquoi nous ajoutons des fichiers `.htaccess` de protection.

**Alternative recommandée** : Si vous êtes sur un plan Hostinger plus avancé, contactez le support Hostinger pour qu'ils configurent le document root vers `public_html/public`.

---

## 🆘 Besoin d'Aide ?

Si vous rencontrez des difficultés avec ces étapes, faites-moi savoir à quelle étape vous êtes bloqué et je vous aiderai plus précisément.
