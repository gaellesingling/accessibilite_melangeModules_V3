# 🔑 Système de Gestion Multi-Clés API avec Rotation Automatique

## 📝 Vue d'ensemble

Le module Text-to-Speech dispose maintenant d'un système complet de gestion multi-clés API permettant :
- ✅ Ajout de plusieurs clés API (Google Cloud, Amazon Polly, Azure, etc.)
- ✅ Définition d'une limite par clé (nombre de requêtes)
- ✅ Suivi en temps réel de l'utilisation
- ✅ Rotation automatique vers la clé suivante
- ✅ Statistiques détaillées
- ✅ Interface admin intuitive

---

## 🎯 Fonctionnalités principales

### 1. Gestion Multi-Clés
- Ajoutez autant de clés API que vous le souhaitez
- Chaque clé peut avoir :
  - Un **label** personnalisé (ex: "API Production", "API Backup")
  - Une **limite** de requêtes (ex: 1000, 5000, 10000)
  - Un compteur d'**usage** en temps réel
  - Un **statut** (Active/Inactive)

### 2. Rotation Automatique
Le système sélectionne automatiquement la prochaine clé disponible :
1. **Clé Active** : Utilise la première clé avec de l'usage disponible
2. **Limite atteinte** : La clé devient automatiquement inactive
3. **Toutes les clés utilisées** : Si rotation automatique activée, tous les compteurs se réinitialisent
4. **Sans clé API** : Utilise la Web Speech API native du navigateur (gratuit)

### 3. Statistiques en Temps Réel
Dashboard avec 4 indicateurs clés :
- 📊 **Clés totales** : Nombre de clés configurées
- ✅ **Clés actives** : Nombre de clés encore disponibles
- 📈 **Usage total** : Nombre total de requêtes consommées
- 🎯 **Limite totale** : Capacité maximale de toutes les clés

### 4. Suivi Détaillé par Clé
Pour chaque clé API :
- **Barre de progression** : Visualisation de l'usage (vert → orange → rouge)
- **Statut visuel** : Badge coloré (actif/inactif)
- **Dernière utilisation** : Horodatage de la dernière requête
- **Clé masquée** : Seuls les 8 premiers et 4 derniers caractères sont visibles

---

## 🛠️ Installation

Remplacez simplement le fichier `module.php` dans votre dossier :
```
wp-content/plugins/accessibility-modular/modules/text-to-speech/module.php
```

---

## 📋 Interface Admin

### Vue d'ensemble
![Interface Admin](ttsadmin.png)

L'interface admin se compose de :

1. **Paramètres généraux**
   - Toggle pour activer/désactiver le module
   - Toggle pour la rotation automatique

2. **Dashboard statistiques**
   - 4 cartes avec métriques clés
   - Design moderne et responsive

3. **Tableau des clés API**
   - Liste complète de toutes les clés
   - Barre de progression par clé
   - Actions (supprimer)
   - Tri automatique par statut

4. **Formulaire d'ajout**
   - Champ pour la clé API
   - Label personnalisé
   - Limite de requêtes
   - Bouton d'ajout

5. **Actions globales**
   - Réinitialiser tous les compteurs
   - Documentation intégrée

---

## 💻 Utilisation

### Ajouter une clé API

1. **Obtenir une clé API** (Google Cloud, Amazon, Azure)
2. **Remplir le formulaire** :
   - **Clé API** : Votre clé complète (ex: `sk-proj-xxxxxxxxxxxxxxxx`)
   - **Label** : Nom descriptif (ex: "API Production")
   - **Limite** : Nombre de requêtes autorisées (ex: 1000)
3. **Cliquer sur "Ajouter"**

### Configuration

```php
// Exemple de configuration
Clé API: sk-proj-abc123...xyz789
Label: API Production
Limite: 1000 requêtes
```

### Rotation automatique

**Avec rotation activée** :
```
Clé 1 (1000/1000) → Inactive
Clé 2 (1000/1000) → Inactive
Clé 3 (500/1000) → Active ✓
→ Le système utilise la Clé 3

Quand toutes sont pleines:
→ Réinitialisation automatique
→ Retour à la Clé 1 (0/1000)
```

**Sans rotation** :
```
Clé 1 (1000/1000) → Inactive
Clé 2 (1000/1000) → Inactive
Clé 3 (1000/1000) → Inactive
→ Retour à Web Speech API native
```

---

## 🔧 Fonctions PHP Disponibles

### Récupérer la clé API active
```php
$tts = new ACC_Module_Text_To_Speech();
$active_key = $tts->get_active_api_key();

if ($active_key) {
    echo "Clé active: " . $active_key;
} else {
    echo "Utilisation de Web Speech API native";
}
```

### Incrémenter l'usage
```php
$tts = new ACC_Module_Text_To_Speech();
$tts->increment_api_usage($api_key);
```

### Obtenir toutes les clés
```php
$tts = new ACC_Module_Text_To_Speech();
$keys = $tts->get_api_keys();

foreach ($keys as $key) {
    echo $key['label'] . ": " . $key['usage'] . "/" . $key['limit'];
}
```

### Obtenir les statistiques
```php
$tts = new ACC_Module_Text_To_Speech();
$info = $tts->get_info();

echo "Nombre de clés: " . $info['api_keys_count'];
echo "Clé active disponible: " . ($info['active_api_key'] ? 'Oui' : 'Non');
```

---

## 📊 Structure des données

### Format de stockage (JSON)
```json
[
  {
    "key": "sk-proj-abc123...xyz789",
    "label": "API Production",
    "limit": 1000,
    "usage": 457,
    "active": true,
    "created": "2025-10-23 10:30:00",
    "last_used": "2025-10-23 14:45:00"
  },
  {
    "key": "sk-proj-def456...uvw012",
    "label": "API Backup",
    "limit": 5000,
    "usage": 0,
    "active": true,
    "created": "2025-10-23 11:00:00",
    "last_used": null
  }
]
```

### Options WordPress
- `acc_tts_api_keys` : JSON des clés API
- `acc_tts_enabled` : Module activé (boolean)
- `acc_tts_auto_rotate` : Rotation automatique (boolean)

---

## 🎨 Design & UX

### Couleurs des barres de progression
- **0-69%** : Vert (#10b981) - Tout va bien
- **70-89%** : Orange (#f59e0b) - Attention
- **90-100%** : Rouge (#ef4444) - Critique

### Badges de statut
- **Active** : Vert avec ✓
- **Inactive** : Rouge avec ✗

### Cartes statistiques
- Design moderne avec ombres
- Grandes valeurs numériques
- Labels clairs et concis
- Responsive (4 colonnes → 2 → 1)

---

## 🔒 Sécurité

### Protection des clés API
- ✅ Affichage masqué (8 premiers + 4 derniers caractères)
- ✅ Sanitization des entrées
- ✅ Vérification des permissions WordPress
- ✅ Nonces pour les actions POST
- ✅ Validation des données avant sauvegarde

### Permissions requises
- `manage_options` : Requis pour toutes les actions admin
- Vérification automatique avec `current_user_can()`

---

## 🚀 Cas d'usage

### Cas 1 : Site à fort trafic
```
Clé 1: 10,000 requêtes/jour
Clé 2: 10,000 requêtes/jour
Clé 3: 10,000 requêtes/jour
→ Total: 30,000 requêtes/jour avec rotation
```

### Cas 2 : Backup automatique
```
Clé principale: 5,000 requêtes
Clé backup: 1,000 requêtes (au cas où)
→ Sécurité en cas de dépassement
```

### Cas 3 : Tests et production
```
Clé test: 100 requêtes (pour développement)
Clé prod: 5,000 requêtes (pour production)
→ Séparation des environnements
```

---

## ⚠️ Limitations

- **Clés API** : Google Cloud, Amazon Polly, Azure doivent être configurées avec leurs SDK respectifs
- **Web Speech API** : Reste la solution par défaut (gratuite, illimitée)
- **Compteurs** : Ne se réinitialisent pas automatiquement par période (manuel ou rotation complète)
- **Webhooks** : Pas de notifications automatiques quand limite atteinte (à implémenter)

---

## 📈 Évolutions futures possibles

1. **Alertes email** : Notification quand une clé atteint 90%
2. **Reset planifié** : Réinitialisation automatique tous les jours/semaines/mois
3. **Logs détaillés** : Historique complet des requêtes
4. **Analytics** : Graphiques d'utilisation dans le temps
5. **API REST** : Endpoint pour consulter les stats via API
6. **Multi-providers** : Support natif Google/Amazon/Azure avec auto-détection

---

## 🐛 Dépannage

### Problème : Les clés ne tournent pas
✅ Vérifiez que "Rotation automatique" est activée
✅ Vérifiez que vous avez plusieurs clés configurées

### Problème : Usage ne s'incrémente pas
✅ Vérifiez que la fonction `increment_api_usage()` est appelée
✅ Vérifiez les permissions WordPress

### Problème : Affichage incorrect dans l'admin
✅ Videz le cache WordPress
✅ Vérifiez que le fichier `module.php` est bien uploadé
✅ Vérifiez les permissions des fichiers (644)

---

## 📞 Support

Pour toute question ou problème :
1. Vérifiez cette documentation
2. Consultez les logs WordPress
3. Testez avec une seule clé d'abord
4. Contactez le support technique

---

## 📄 Licence

GPL v2 or later (identique au plugin principal)

---

**Version** : 1.1.0  
**Date** : 23 octobre 2025  
**Auteur** : Accessibility Modular Team  
**Statut** : ✅ Production Ready

---

## 🎉 Conclusion

Ce système de gestion multi-clés API offre une solution professionnelle et scalable pour gérer vos APIs Text-to-Speech avec :
- Interface admin intuitive
- Rotation automatique intelligente
- Suivi en temps réel
- Sécurité renforcée
- Extensibilité maximale

**Profitez d'une gestion simplifiée de vos clés API ! 🚀**
