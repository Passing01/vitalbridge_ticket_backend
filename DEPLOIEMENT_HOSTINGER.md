# Instructions de Déploiement - Correction .htaccess Hostinger

## 🚨 Nouveau Problème : Redirection HTML

Vous recevez maintenant une **redirection HTML** au lieu d'une réponse JSON. Cela signifie que soit :
1. Le fichier `.htaccess` n'a **pas été téléchargé** correctement sur Hostinger
2. Il y a une **redirection forcée** dans votre panneau Hostinger
3. Le **cache** du serveur n'a pas été vidé

## ✅ ÉTAPE 1 : Vérifier que le Fichier est Bien sur le Serveur

### Via le Gestionnaire de Fichiers Hostinger

1. Connectez-vous à **Hostinger**
2. Allez dans **Fichiers** → **Gestionnaire de fichiers**
3. Naviguez vers : `/domains/vitalbridge.kgslab.com/public_html/`
4. Cherchez le fichier `.htaccess` (cochez "Afficher les fichiers cachés" si nécessaire)
5. **Ouvrez-le** et vérifiez son contenu

**Le fichier doit contenir** :
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # IMPORTANT: Ne jamais faire de redirection HTTP externe (R=301/302)
    # pour éviter de perdre les méthodes POST
    
    # Si la requête cible déjà le dossier public, ne rien faire
    RewriteCond %{REQUEST_URI} ^/public/
    RewriteRule ^ - [L]
    
    # Si le fichier ou dossier existe à la racine, ne pas toucher
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # Pour toutes les autres requêtes, faire un rewrite INTERNE vers public/
    RewriteRule ^(.*)$ public/$1 [L,QSA]
</IfModule>
```

### Via SSH (si vous avez accès)

```bash
ssh u687127774@vitalbridge.kgslab.com
cat /home/u687127774/domains/vitalbridge.kgslab.com/public_html/.htaccess
```

## ✅ ÉTAPE 2 : Télécharger ou Mettre à Jour le Fichier

### Fichier à Télécharger

Le fichier correct se trouve dans votre projet local :
- **Chemin local** : `/.htaccess` (à la racine du projet)
- **Destination Hostinger** : `/home/u687127774/domains/vitalbridge.kgslab.com/public_html/.htaccess`

### Comment Télécharger

**Via le Gestionnaire de Fichiers** :
1. Ouvrez le Gestionnaire de fichiers Hostinger
2. Naviguez vers `/domains/vitalbridge.kgslab.com/public_html/`
3. Si un fichier `.htaccess` existe déjà, **supprimez-le**
4. Cliquez sur **Télécharger** (Upload)
5. Sélectionnez le fichier `.htaccess` de votre projet local
6. Assurez-vous qu'il est bien à la racine de `public_html`

**Via FTP** (FileZilla, etc.) :
1. Connectez-vous à votre serveur Hostinger
2. Allez dans `/domains/vitalbridge.kgslab.com/public_html/`
3. Glissez-déposez le fichier `.htaccess` (remplacer si existe)

## ✅ ÉTAPE 3 : Vérifier les Redirections dans Hostinger

⚠️ **TRÈS IMPORTANT** : Hostinger peut avoir des redirections configurées dans le panneau de contrôle qui ont priorité sur le `.htaccess`

1. Connectez-vous au **panneau Hostinger**
2. Allez dans **Domaines**
3. Cliquez sur `vitalbridge.kgslab.com`
4. Cherchez une section **Redirections** ou **Redirects**
5. **Supprimez toute redirection** configurée pour ce domaine

## ✅ ÉTAPE 4 : Vider le Cache

Dans le panneau Hostinger :
1. Allez dans **Website** ou **Site Web**
2. Cherchez **Cache** ou **Performance**
3. Cliquez sur **Clear Cache** ou **Vider le cache**
4. **Attendez 2-3 minutes** avant de tester

## ✅ ÉTAPE 5 : Tester avec Plus d'Informations

Utilisez cette commande pour voir **toutes les redirections** :

```bash
curl -X POST https://vitalbridge.kgslab.com/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"phone":"123456789", "name":"Test User", "email":"test@example.com"}' \
  -v 2>&1 | grep -E "(HTTP|Location|<)"
```

**Que chercher** :
- `HTTP/1.1 301` ou `HTTP/1.1 302` = Redirection (MAUVAIS !)
- `Location:` = URL vers laquelle vous êtes redirigé
- `HTTP/1.1 200` = Succès (BON !)

## 🔧 Solution Alternative : Changer le Document Root

Si tout échoue, la **meilleure solution** est de changer le document root :

1. Dans le panneau Hostinger, allez dans **Domaines**
2. Cliquez sur votre domaine `vitalbridge.kgslab.com`
3. Cherchez **Document Root** ou **Répertoire Web**
4. Changez de :
   ```
   /home/u687127774/domains/vitalbridge.kgslab.com/public_html
   ```
   vers :
   ```
   /home/u687127774/domains/vitalbridge.kgslab.com/public_html/public
   ```

✅ **Avantage** : Vous n'aurez plus besoin du fichier `.htaccess` à la racine !

## 📞 Si Ça Ne Fonctionne Toujours Pas

Faites-moi savoir et envoyez-moi :
1. Le contenu exact du fichier `.htaccess` sur le serveur
2. Le résultat complet de la commande curl avec `-v`
3. Une capture d'écran de la section "Redirections" dans Hostinger

