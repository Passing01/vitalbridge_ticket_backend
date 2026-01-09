# Plan d'Implémentation - Système Qmatic

## Vue d'ensemble
Développement d'un système de gestion de files d'attente (QMS) type Qmatic adapté au Burkina Faso, en parallèle du système VitalBridge existant.

## Architecture

### Modules à Développer

#### 1. Module Services (Qmatic)
- **Tables** : `qmatic_services`
- **Modèles** : `QmaticService`
- **Contrôleurs** : `QmaticServiceController`
- **Fonctionnalités** :
  - CRUD des services (création, modification, suppression)
  - Codes de service (A, B, C, etc.)
  - Horaires par service
  - Activation/désactivation

#### 2. Module Tickets (Qmatic)
- **Tables** : `qmatic_tickets`
- **Modèles** : `QmaticTicket`
- **Contrôleurs** : `QmaticTicketController`
- **Fonctionnalités** :
  - Prise de ticket (borne, web, mobile)
  - Attribution automatique de numéro
  - Génération avec numéro complet (ex: A023)
  - Gestion des priorités (normal, senior, VIP, urgence)

#### 3. Module Files d'Attente (Qmatic)
- **Tables** : `qmatic_queues`
- **Modèles** : `QmaticQueue`
- **Contrôleurs** : `QmaticQueueController`
- **Fonctionnalités** :
  - Files par service
  - Ordre FIFO
  - Gestion des priorités
  - Réaffectation manuelle

#### 4. Module Guichets
- **Tables** : `qmatic_counters`
- **Modèles** : `QmaticCounter`
- **Contrôleurs** : `QmaticCounterController`
- **Fonctionnalités** :
  - CRUD des guichets
  - Attribution agent-guichet
  - Statut (actif/inactif)

#### 5. Interface Agent
- **Contrôleurs** : `QmaticAgentController`
- **Vues** : Dashboard agent
- **Fonctionnalités** :
  - Connexion sécurisée
  - Appeler le suivant
  - Rappel du ticket
  - Marquer comme servi/absent/annulé

#### 6. Affichage Public
- **Contrôleurs** : `QmaticDisplayController`
- **Vues** : Écran public temps réel
- **Fonctionnalités** :
  - Affichage numéro appelé
  - Affichage guichet
  - Support audio (optionnel)
  - Mode plein écran

#### 7. Administration
- **Contrôleurs** : `QmaticAdminController`
- **Fonctionnalités** :
  - Gestion utilisateurs Qmatic
  - Gestion des guichets
  - Configuration priorités
  - Paramétrage multilingue

#### 8. Statistiques & Rapports
- **Contrôleurs** : `QmaticReportController`
- **Fonctionnalités** :
  - Temps d'attente moyen
  - Temps de service moyen
  - Performance par agent/guichet
  - Nombre de tickets par service
  - Export PDF/Excel

## Structure de la Base de Données

### Tables à Créer

1. **qmatic_services**
   - id (UUID)
   - health_center_id (UUID) - FK vers health_centers
   - code (string) - Ex: A, B, C
   - name (string)
   - description (text, nullable)
   - priority_order (integer)
   - is_active (boolean)
   - working_hours (json) - {day: {start, end}}
   - timestamps

2. **qmatic_counters**
   - id (UUID)
   - health_center_id (UUID)
   - code (string) - Ex: G1, G2
   - name (string)
   - service_ids (json) - Services supportés
   - is_active (boolean)
   - current_agent_id (UUID, nullable)
   - timestamps

3. **qmatic_tickets**
   - id (UUID)
   - health_center_id (UUID)
   - service_id (UUID)
   - ticket_number (string) - Ex: A023
   - sequence_number (integer)
   - priority (enum: normal, senior, vip, urgent)
   - status (enum: waiting, called, serving, served, absent, cancelled)
   - counter_id (UUID, nullable)
   - agent_id (UUID, nullable)
   - called_at (timestamp, nullable)
   - served_at (timestamp, nullable)
   - completed_at (timestamp, nullable)
   - wait_time (integer, nullable) - en minutes
   - service_time (integer, nullable) - en minutes
   - timestamps

4. **qmatic_ticket_calls**
   - id (UUID)
   - ticket_id (UUID)
   - counter_id (UUID)
   - agent_id (UUID)
   - called_at (timestamp)
   - timestamps

5. **qmatic_settings**
   - id (UUID)
   - health_center_id (UUID)
   - key (string)
   - value (json)
   - timestamps

## Étapes d'Implémentation

### Phase 1 : Base de données (Priorité: HAUTE)
- [ ] Créer migrations pour toutes les tables
- [ ] Créer les modèles Eloquent
- [ ] Définir les relations entre modèles
- [ ] Créer les seeders de test

### Phase 2 : Backend - Services & Guichets (Priorité: HAUTE)
- [ ] Contrôleur QmaticServiceController
- [ ] Contrôleur QmaticCounterController
- [ ] Routes API pour services
- [ ] Routes API pour guichets
- [ ] Validation des données

### Phase 3 : Backend - Tickets & Files (Priorité: HAUTE)
- [ ] Contrôleur QmaticTicketController
- [ ] Contrôleur QmaticQueueController
- [ ] Logique de génération de numéros
- [ ] Logique de gestion des priorités
- [ ] WebSocket pour temps réel (Laravel Reverb/Pusher)

### Phase 4 : Interface Agent (Priorité: HAUTE)
- [ ] Dashboard agent avec Blade
- [ ] Bouton "Appeler le suivant"
- [ ] Bouton "Rappel"
- [ ] Actions: Servi/Absent/Annulé
- [ ] Affichage file d'attente

### Phase 5 : Affichage Public (Priorité: MOYENNE)
- [ ] Écran d'affichage temps réel
- [ ] WebSocket pour mises à jour
- [ ] Mode plein écran
- [ ] Support audio (Text-to-Speech)

### Phase 6 : Administration (Priorité: MOYENNE)
- [ ] Interface admin Qmatic
- [ ] Gestion utilisateurs
- [ ] Configuration système
- [ ] Paramétrage multilingue

### Phase 7 : Statistiques & Rapports (Priorité: BASSE)
- [ ] Dashboard statistiques
- [ ] Génération rapports PDF
- [ ] Export Excel
- [ ] Graphiques de performance

### Phase 8 : Multilingue (Priorité: BASSE)
- [ ] Support Français
- [ ] Support Mooré
- [ ] Support Dioula
- [ ] Support Fulfuldé
- [ ] Fichiers de traduction Laravel

### Phase 9 : Tests & Optimisation (Priorité: BASSE)
- [ ] Tests unitaires
- [ ] Tests d'intégration
- [ ] Optimisation requêtes DB
- [ ] Cache Redis

## Routes Structure

```
/qmatic
├── / (Présentation - FAIT)
├── /admin
│   ├── /services (CRUD services)
│   ├── /counters (CRUD guichets)
│   ├── /settings (Configuration)
│   └── /users (Gestion utilisateurs)
├── /kiosk (Prise de ticket - borne)
├── /agent
│   ├── /dashboard (Interface agent)
│   ├── /call (Appeler ticket)
│   └── /actions (Servi/Absent/Annulé)
├── /display (Affichage public)
└── /reports (Statistiques)
```

## Technologies

- **Backend**: Laravel 11
- **Base de données**: MySQL
- **Frontend**: Blade + TailwindCSS/Vanilla CSS
- **Temps réel**: Laravel Reverb ou Pusher
- **PDF**: DomPDF ou Snappy
- **Excel**: Laravel Excel
- **Audio**: Web Speech API (navigateur)

## Notes Importantes

1. **Isolation**: Toutes les tables Qmatic ont le préfixe `qmatic_` pour éviter les conflits
2. **Multi-tenant**: Intégration avec `health_center_id` existant
3. **Authentification**: Utilise le système auth Laravel existant
4. **Rôles**: Ajouter rôles `qmatic_agent`, `qmatic_admin`
5. **API**: Créer API REST pour intégrations futures
6. **WebSocket**: Pour mises à jour temps réel des écrans

## Prochaines Étapes Immédiates

1. Créer les migrations de base de données
2. Créer les modèles Eloquent
3. Implémenter la gestion des services
4. Implémenter la prise de ticket
5. Créer l'interface agent
