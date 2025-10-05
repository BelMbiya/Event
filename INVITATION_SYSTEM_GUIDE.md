# Guide du Système d'Invitations Sans Invités

## Vue d'ensemble

Le système d'invitations a été amélioré pour permettre la création d'invitations même sans invités. Une fois qu'un invité est ajouté à l'événement, il aura automatiquement accès à l'invitation existante.

## Fonctionnalités Ajoutées

### 1. Création d'Invitations Sans Invités

- **Avant** : Impossible de créer des invitations sans invités
- **Maintenant** : Création d'une invitation générale pour l'événement même sans invités

### 2. Association Automatique des Invités

- **Automatique** : Lors de l'ajout d'un nouvel invité, une invitation personnalisée est créée automatiquement
- **Basée sur l'invitation générale** : Le contenu de l'invitation générale est copié vers l'invitation de l'invité

### 3. Nouvelles Routes

```
POST /events/{event_id}/guests/{guest_id}/associate-invitation
POST /events/{event_id}/associate-all-guests
GET  /invitation/general/{unique_code}
```

## Utilisation

### Créer une Invitation Sans Invités

1. Allez dans la section "Invitations"
2. Cliquez sur "Créer une invitation" pour un événement
3. Même sans invités, l'invitation sera créée comme "invitation générale"
4. L'URL sera : `/invitation/general/{unique_code}`

### Ajouter des Invités Après

1. Ajoutez des invités à l'événement
2. Chaque invité recevra automatiquement une invitation personnalisée
3. L'URL sera : `/invitation/{unique_code}`

### Association Manuelle

Si vous voulez associer manuellement des invités :

```javascript
// Associer un invité spécifique
POST /events/{event_id}/guests/{guest_id}/associate-invitation

// Associer tous les invités sans invitation
POST /events/{event_id}/associate-all-guests
```

## Structure de la Base de Données

### Table `invitations`
- `guest_id` peut maintenant être `NULL` pour les invitations générales
- `unique_code` reste unique pour chaque invitation
- `invitation_url` pointe vers l'URL appropriée

### Table `contents`
- `guest_id` peut être `NULL` pour le contenu général
- Chaque invitation a son propre contenu

## Observer Automatique

Un `GuestObserver` a été ajouté pour :
- Créer automatiquement une invitation lors de l'ajout d'un invité
- Supprimer l'invitation lors de la suppression d'un invité
- Copier le contenu de l'invitation générale

## Avantages

1. **Flexibilité** : Créer des invitations avant d'avoir la liste complète des invités
2. **Automatisation** : Plus besoin d'associer manuellement chaque invité
3. **Cohérence** : Tous les invités ont le même contenu de base
4. **Rétrocompatibilité** : Le système existant continue de fonctionner

## Exemples d'Utilisation

### Scénario 1 : Événement Sans Invités
```php
// Créer une invitation générale
$invitation = Invitation::create([
    'event_id' => $event_id,
    'guest_id' => null, // Invitation générale
    'unique_code' => Str::uuid(),
    'status' => 'sent',
    'invitation_url' => url("/invitation/general/{$unique_code}")
]);
```

### Scénario 2 : Ajout d'Invités
```php
// L'observer crée automatiquement l'invitation
$guest = Guest::create([
    'event_id' => $event_id,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com'
]);
// Une invitation est automatiquement créée pour cet invité
```

### Scénario 3 : Association Manuelle
```php
// Associer tous les invités sans invitation
$response = $this->post("/events/{$event_id}/associate-all-guests");
```

## Migration et Compatibilité

- **Aucune migration nécessaire** : Le système est rétrocompatible
- **Données existantes** : Toutes les invitations existantes continuent de fonctionner
- **Nouvelles fonctionnalités** : Disponibles immédiatement

## Logs et Debugging

Le système enregistre des logs pour :
- Création d'invitations générales
- Association automatique d'invités
- Copie de contenu entre invitations

Consultez `storage/logs/laravel.log` pour suivre les opérations.

## Support

Pour toute question ou problème :
1. Vérifiez les logs Laravel
2. Testez avec un événement de test
3. Vérifiez que l'observer est bien enregistré dans `AppServiceProvider`
