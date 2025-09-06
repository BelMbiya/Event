# 📋 PROCESSUS COMPLET DE CRÉATION D'INVITATION ET CONTENU

## 🔄 **FLUX COMPLET DU PROCESSUS**

### **1. CRÉATION D'UN ÉVÉNEMENT**
```
Dashboard → Créer Événement → Formulaire Événement → Sauvegarde
```
- **Route**: `POST /event` (EventController@store)
- **Vue**: `event/create.blade.php`
- **Résultat**: Événement créé avec ID unique

### **2. AJOUT D'INVITÉS À L'ÉVÉNEMENT**
```
Événement → Gérer Invités → Ajouter Invités → Sauvegarde
```
- **Route**: `POST /guests` (GuestController@store)
- **Vue**: `guests/create.blade.php`
- **Résultat**: Invités ajoutés avec types et catégories

### **3. CRÉATION D'INVITATIONS**
```
Événement → Créer Invitations → Formulaire Avancé → Sauvegarde
```

#### **3.1. Route de Création**
- **Route**: `GET /invitation/create/{event_id}` (InvitationController@create)
- **Vue**: `invitation/create-advanced.blade.php`
- **Contrôleur**: `InvitationController@create`

#### **3.2. Processus de Sauvegarde**
- **Route**: `POST /invitation` (InvitationController@store)
- **Contrôleur**: `InvitationController@store`
- **Actions**:
  1. Validation des données
  2. Création de l'invitation avec `unique_code`
  3. Création automatique du contenu de base
  4. Retour JSON avec modal de confirmation

### **4. PERSONNALISATION DU CONTENU**
```
Invitation → Éditer Contenu → Formulaire Contenu → Sauvegarde
```

#### **4.1. Route d'Édition**
- **Route**: `GET /invitation/{invitation_id}/content/edit`
- **Contrôleur**: `ContentController@edit`
- **Vue**: `invitation/edit-content.blade.php`

#### **4.2. Processus de Mise à Jour**
- **Route**: `PUT /invitation/{invitation_id}/content`
- **Contrôleur**: `ContentController@update`
- **Actions**:
  1. Validation des données
  2. Upload des fichiers (images, galerie)
  3. Traitement des données JSON (thème, CTA, programme)
  4. Sauvegarde ou création du contenu
  5. Retour JSON avec modal de confirmation

## 🗂️ **STRUCTURE DES DONNÉES**

### **Table `invitations`**
```sql
- id (PK)
- event_id (FK)
- guest_id (FK)
- unique_code (unique)
- status
- sent_at
- opened_at
- created_at
- updated_at
```

### **Table `contents`**
```sql
- id (PK)
- invitation_id (FK)
- event_id (FK)
- guest_id (FK)
- unique_code
- couple
- epoux, epouse
- Famille_epoux, Famille_epouse
- event_datetime
- venue_name, venue_address_*
- hero_image_path
- gallery (JSON)
- body_html
- intro_1, intro_2
- schedule (JSON)
- theme (JSON)
- cta (JSON)
- drinks (JSON)
- status
- published_at
```

## 🔗 **RELATIONS ENTRE MODÈLES**

```
Event (1) ←→ (N) Invitation
Event (1) ←→ (N) Guest
Event (1) ←→ (N) Content
Invitation (1) ←→ (1) Content
Guest (1) ←→ (N) Invitation
```

## 🛠️ **CONTRÔLEURS ET ROUTES**

### **InvitationController**
- `index()` → Liste des invitations
- `create($event_id)` → Formulaire de création
- `store(Request)` → Sauvegarde invitation + contenu
- `show($unique_code)` → Affichage invitation
- `destroy($id)` → Suppression

### **ContentController**
- `edit($invitation_id)` → Formulaire d'édition contenu
- `update(Request, $invitation_id)` → Mise à jour contenu

### **Routes Principales**
```php
// Invitations
Route::get('invitation/create/{event_id}', 'create')->name('invitation.create');
Route::post('invitation', 'store')->name('invitation.store');
Route::get('invitation/{unique_code}', 'show')->name('invitation.show');

// Contenu
Route::get('/invitation/{invitation_id}/content/edit', 'edit')->name('invitation.content.edit');
Route::put('/invitation/{invitation_id}/content', 'update')->name('invitation.content.update');
```

## 🎨 **VUES ET INTERFACES**

### **1. Création d'Invitation** (`create-advanced.blade.php`)
- **Onglets**: Informations de base, Contenu, Design, Images, Options
- **JavaScript**: Gestion AJAX, modals, validation
- **Fonctionnalités**: Upload d'images, éditeur de texte, prévisualisation

### **2. Édition de Contenu** (`edit-content.blade.php`)
- **Onglets**: Informations de base, Contenu & Textes, Design & Thème
- **JavaScript**: Gestion AJAX, modals de retour
- **Fonctionnalités**: Éditeur WYSIWYG, upload d'images, gestion des thèmes

### **3. Affichage d'Invitation** (`Invitation_3.blade.php`)
- **Design**: Template moderne avec animations
- **Contenu dynamique**: Données de l'invité, contenu personnalisé
- **Fonctionnalités**: RSVP, téléchargement PDF, partage

## ⚡ **FONCTIONNALITÉS AJAX**

### **Création d'Invitation**
```javascript
// Formulaire avec AJAX
document.getElementById('invitationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Envoi AJAX avec FormData
    // Affichage modal de résultat
});
```

### **Édition de Contenu**
```javascript
// Formulaire avec AJAX
document.getElementById('editContentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Envoi AJAX avec FormData
    // Affichage modal de résultat
});
```

## 🔧 **CORRECTION DU PROBLÈME DE SAUVEGARDE**

### **Problème Identifié**
Le bouton de sauvegarde ne fonctionnait pas car :
1. **Méthode HTTP**: Le JavaScript utilisait `POST` au lieu de `PUT`
2. **Headers manquants**: `X-HTTP-Method-Override` manquant

### **Solution Appliquée**
```javascript
fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-HTTP-Method-Override': 'PUT'  // ← AJOUTÉ
    }
})
```

## 📱 **FLUX UTILISATEUR COMPLET**

1. **Dashboard** → Sélectionner événement
2. **Créer Invitation** → Remplir formulaire avancé
3. **Sauvegarde** → Modal de confirmation
4. **Éditer Contenu** → Personnaliser l'invitation
5. **Sauvegarde** → Modal de confirmation
6. **Prévisualiser** → Voir l'invitation finale
7. **Partager** → Envoyer aux invités

## 🎯 **POINTS CLÉS À RETENIR**

- **Invitation** = Container principal avec `unique_code`
- **Content** = Données personnalisables de l'invitation
- **Relation** = 1 invitation ↔ 1 contenu
- **AJAX** = Toutes les sauvegardes utilisent AJAX
- **Modals** = Retour utilisateur avec modals
- **Validation** = Côté client et serveur
- **Upload** = Gestion des images et fichiers
