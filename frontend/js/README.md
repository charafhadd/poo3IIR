# Documentation JavaScript - Cabinet Médical

Ce dossier contient tous les fichiers JavaScript pour améliorer l'expérience utilisateur du frontend.

## 📁 Structure des fichiers

### `main.js`
Fichier principal contenant les fonctions communes utilisées sur toutes les pages :
- **Notifications Toast** : Affichage de messages temporaires (succès, erreur, info, warning)
- **Utilitaires de date** : Formatage de dates et heures
- **Gestion des alertes** : Conversion automatique des alertes PHP en notifications
- **Navigation** : Mise en surbrillance du lien actif dans la navbar
- **Animations** : Animations d'entrée pour les cartes et éléments
- **Utilitaires généraux** : Fonctions de confirmation, loader, copie presse-papiers, etc.

### `form-validation.js`
Validation des formulaires côté client avec feedback en temps réel :
- **Validation inscription** : Nom, prénom, email, téléphone, mot de passe, confirmation
- **Validation connexion** : Email et mot de passe
- **Validation prise de RDV** : Médecin, date, heure, vérification disponibilité
- **Messages d'erreur** : Affichage visuel des erreurs avec classes Bootstrap
- **Validation avant soumission** : Empêche la soumission si des erreurs existent

### `medecins.js`
Fonctionnalités pour la page des médecins :
- **Recherche** : Recherche en temps réel par nom, prénom ou spécialité
- **Filtres par spécialité** : Boutons pour filtrer par spécialité médicale
- **Tri** : Tri par nom, spécialité, tarif (croissant/décroissant)
- **Compteur** : Affichage du nombre de médecins trouvés
- **Animations** : Animations au survol et à l'apparition des cartes

### `rendezvous.js`
Gestion des rendez-vous côté client :
- **Filtres** : Filtrage par statut (en attente, confirmé, annulé, terminé)
- **Recherche** : Recherche par nom de médecin
- **Tri** : Tri par date, statut, médecin
- **Annulation améliorée** : Confirmation personnalisée avant annulation
- **Statistiques** : Mise à jour dynamique des statistiques
- **Export CSV** : Exportation des rendez-vous au format CSV

### `dossier-medical.js`
Améliorations pour la page dossier médical :
- **Vues améliorées** : Amélioration des vues cartes, timeline et tableau
- **Recherche** : Recherche dans les dossiers par médecin, diagnostic, traitement
- **Modal de détails** : Affichage détaillé d'un dossier dans une modal Bootstrap
- **Export PDF** : Génération de PDF avec jsPDF (chargement automatique)
- **Compteur** : Compteur de dossiers visibles
- **Impression améliorée** : Styles d'impression personnalisés

## 🚀 Utilisation

Les scripts sont automatiquement chargés dans chaque page PHP selon leurs besoins :

- **Toutes les pages** : `main.js`
- **login.php** : `main.js` + `form-validation.js`
- **signup.php** : `main.js` + `form-validation.js`
- **medecins.php** : `main.js` + `medecins.js`
- **prendre_rdv.php** : `main.js` + `form-validation.js`
- **rendezvous.php** : `main.js` + `rendezvous.js`
- **dossier_medical.php** : `main.js` + `dossier-medical.js`

## 📝 Fonctions principales disponibles

### Notifications
```javascript
showToast('Message de succès', 'success', 3000);
showToast('Message d\'erreur', 'error', 5000);
showToast('Avertissement', 'warning', 4000);
showToast('Information', 'info', 3000);
```

### Formatage de dates
```javascript
formatDate('2024-01-15'); // "15 janvier 2024"
formatDateShort('2024-01-15'); // "15/01/2024"
formatTime('14:30:00'); // "14:30"
```

### Confirmation d'action
```javascript
const confirmed = await confirmAction('Êtes-vous sûr ?', 'Confirmation');
if (confirmed) {
    // Action confirmée
}
```

### Loader
```javascript
showLoader(true);  // Afficher
showLoader(false); // Masquer
```

## 🎨 Personnalisation

### Modifier les couleurs des notifications
Les notifications utilisent les classes Bootstrap :
- `bg-success` pour succès
- `bg-danger` pour erreur
- `bg-warning` pour avertissement
- `bg-primary` pour information

### Modifier les durées d'affichage
Par défaut, les notifications s'affichent pendant 3 secondes. Vous pouvez modifier cette valeur dans l'appel de `showToast()`.

### Ajouter de nouvelles validations
Dans `form-validation.js`, ajoutez vos propres fonctions de validation et appelez-les dans les écouteurs d'événements appropriés.

## 🔧 Dépendances

- **Bootstrap 5.3.2** : Pour les composants UI (modals, tooltips, popovers, etc.)
- **Font Awesome 6.4.0** : Pour les icônes
- **jsPDF** (optionnel) : Pour l'export PDF, chargé dynamiquement si nécessaire

## 📱 Compatibilité

Les scripts sont compatibles avec :
- Chrome/Edge (dernières versions)
- Firefox (dernières versions)
- Safari (dernières versions)
- Navigateurs mobiles modernes

## 🐛 Dépannage

### Les scripts ne se chargent pas
1. Vérifiez que les chemins dans les pages PHP sont corrects (`js/main.js`)
2. Ouvrez la console du navigateur (F12) pour voir les erreurs
3. Vérifiez que Bootstrap est chargé avant les scripts personnalisés

### Les validations ne fonctionnent pas
1. Vérifiez que `form-validation.js` est bien inclus dans la page
2. Vérifiez que les noms des champs correspondent à ceux attendus
3. Ouvrez la console pour voir les erreurs JavaScript

### Les notifications ne s'affichent pas
1. Vérifiez que Bootstrap JS est chargé
2. Vérifiez que `main.js` est inclus avant les autres scripts
3. Vérifiez la console pour les erreurs

## 📚 Ressources

- [Documentation Bootstrap 5](https://getbootstrap.com/docs/5.3/)
- [Documentation Font Awesome](https://fontawesome.com/docs)
- [Documentation jsPDF](https://github.com/parallax/jsPDF)

## 📄 Licence

Ce code fait partie du projet de gestion de cabinet médical et suit la même licence que le projet principal.

