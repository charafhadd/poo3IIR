/**
 * Fichier JavaScript principal - Cabinet Médical
 * Fonctions communes utilisées sur toutes les pages
 */

// ============================================
// UTILITAIRES GÉNÉRAUX
// ============================================

/**
 * Affiche une notification toast (message temporaire)
 * @param {string} message - Message à afficher
 * @param {string} type - Type de notification (success, error, warning, info)
 * @param {number} duree - Durée d'affichage en ms (défaut: 3000)
 */
function showToast(message, type = 'info', duree = 3000) {
    // Créer le conteneur toast s'il n'existe pas
    let conteneurToast = document.getElementById('toast-container');
    if (!conteneurToast) {
        conteneurToast = document.createElement('div');
        conteneurToast.id = 'toast-container';
        conteneurToast.className = 'position-fixed top-0 end-0 p-3';
        conteneurToast.style.zIndex = '9999';
        document.body.appendChild(conteneurToast);
    }

    // Créer le toast
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'primary'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    const icones = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas ${icones[type] || icones.info} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    conteneurToast.appendChild(toast);
    const toastBootstrap = new bootstrap.Toast(toast, { delay: duree });
    toastBootstrap.show();

    // Supprimer le toast du DOM après fermeture
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * Formate une date au format français
 * @param {string} chaineDate - Date au format ISO
 * @returns {string} Date formatée (ex: "15 janvier 2024")
 */
function formatDate(chaineDate) {
    const date = new Date(chaineDate);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('fr-FR', options);
}

/**
 * Formate une date courte (ex: 15/01/2024)
 * @param {string} chaineDate - Date au format ISO
 * @returns {string} Date formatée
 */
function formatDateShort(chaineDate) {
    const date = new Date(chaineDate);
    const jour = String(date.getDate()).padStart(2, '0');
    const mois = String(date.getMonth() + 1).padStart(2, '0');
    const annee = date.getFullYear();
    return `${jour}/${mois}/${annee}`;
}

/**
 * Formate une heure (ex: 14:30)
 * @param {string} chaineHeure - Heure au format HH:MM:SS
 * @returns {string} Heure formatée
 */
function formatTime(chaineHeure) {
    if (!chaineHeure) return '';
    return chaineHeure.substring(0, 5);
}

/**
 * Vérifie si une date est dans le futur
 * @param {string} chaineDate - Date à vérifier
 * @returns {boolean}
 */
function isFutureDate(chaineDate) {
    const date = new Date(chaineDate);
    const aujourdhui = new Date();
    aujourdhui.setHours(0, 0, 0, 0);
    return date >= aujourdhui;
}

/**
 * Vérifie si une date est dans le passé
 * @param {string} chaineDate - Date à vérifier
 * @returns {boolean}
 */
function isPastDate(chaineDate) {
    return !isFutureDate(chaineDate);
}

/**
 * Confirme une action avec une boîte de dialogue personnalisée
 * @param {string} message - Message de confirmation
 * @param {string} titre - Titre de la boîte de dialogue
 * @returns {Promise<boolean>}
 */
function confirmAction(message, titre = 'Confirmation') {
    return new Promise((resoudre) => {
        // Créer une modal Bootstrap pour la confirmation
        const htmlModal = `
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${titre}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="confirmBtn">Confirmer</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Supprimer l'ancienne modal si elle existe
        const ancienneModal = document.getElementById('confirmModal');
        if (ancienneModal) {
            ancienneModal.remove();
        }

        document.body.insertAdjacentHTML('beforeend', htmlModal);
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        document.getElementById('confirmBtn').addEventListener('click', () => {
            modal.hide();
            resoudre(true);
        });

        modal._element.addEventListener('hidden.bs.modal', () => {
            modal._element.remove();
            resoudre(false);
        });

        modal.show();
    });
}

/**
 * Affiche un loader pendant une opération
 * @param {boolean} afficher - Afficher ou masquer le loader
 */
function showLoader(afficher = true) {
    let loader = document.getElementById('global-loader');
    if (!loader && afficher) {
        loader = document.createElement('div');
        loader.id = 'global-loader';
        loader.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        loader.style.backgroundColor = 'rgba(0,0,0,0.5)';
        loader.style.zIndex = '9999';
        loader.innerHTML = `
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Chargement...</span>
            </div>
        `;
        document.body.appendChild(loader);
    } else if (loader && !afficher) {
        loader.remove();
    }
}

/**
 * Désactive/active un formulaire
 * @param {HTMLFormElement} formulaire - Formulaire à désactiver/activer
 * @param {boolean} desactive - État désactivé
 */
function toggleForm(formulaire, desactive) {
    const champs = formulaire.querySelectorAll('input, select, textarea, button');
    champs.forEach(champ => {
        champ.disabled = desactive;
    });
}

/**
 * Initialise les tooltips Bootstrap sur la page
 */
function initTooltips() {
    const listeTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    listeTooltips.map(function (elementTooltip) {
        return new bootstrap.Tooltip(elementTooltip);
    });
}

/**
 * Initialise les popovers Bootstrap sur la page
 */
function initPopovers() {
    const listePopovers = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    listePopovers.map(function (elementPopover) {
        return new bootstrap.Popover(elementPopover);
    });
}

/**
 * Scroll fluide vers un élément
 * @param {string|HTMLElement} cible - Sélecteur CSS ou élément HTML
 * @param {number} decalage - Décalage en pixels
 */
function smoothScrollTo(cible, decalage = 0) {
    const element = typeof cible === 'string' ? document.querySelector(cible) : cible;
    if (element) {
        const positionElement = element.getBoundingClientRect().top;
        const positionDecalee = positionElement + window.pageYOffset - decalage;
        window.scrollTo({
            top: positionDecalee,
            behavior: 'smooth'
        });
    }
}

/**
 * Copie du texte dans le presse-papiers
 * @param {string} texte - Texte à copier
 * @returns {Promise<boolean>}
 */
async function copyToClipboard(texte) {
    try {
        await navigator.clipboard.writeText(texte);
        showToast('Copié dans le presse-papiers', 'success', 2000);
        return true;
    } catch (erreur) {
        showToast('Erreur lors de la copie', 'error');
        return false;
    }
}

// ============================================
// GESTION DES ALERTES
// ============================================

/**
 * Convertit les alertes PHP en notifications JavaScript
 */
function initAlerts() {
    const alertes = document.querySelectorAll('.alert');
    alertes.forEach(alerte => {
        // Auto-fermeture après 5 secondes pour les alertes de succès
        if (alerte.classList.contains('alert-success')) {
            setTimeout(() => {
                const alerteBootstrap = new bootstrap.Alert(alerte);
                alerteBootstrap.close();
            }, 5000);
        }

        // Animation d'entrée
        alerte.style.opacity = '0';
        alerte.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alerte.style.transition = 'all 0.3s ease';
            alerte.style.opacity = '1';
            alerte.style.transform = 'translateY(0)';
        }, 100);
    });
}

// ============================================
// NAVBAR ET NAVIGATION
// ============================================

/**
 * Met en surbrillance le lien actif dans la navbar
 */
function highlightActiveNav() {
    const cheminActuel = window.location.pathname;
    const liensNav = document.querySelectorAll('.navbar-nav .nav-link');
    
    liensNav.forEach(lien => {
        const cheminLien = new URL(lien.href).pathname;
        if (cheminActuel.includes(cheminLien.split('/').pop())) {
            lien.classList.add('active');
        } else {
            lien.classList.remove('active');
        }
    });
}

// ============================================
// INITIALISATION AU CHARGEMENT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips et popovers
    initTooltips();
    initPopovers();
    
    // Gérer les alertes
    initAlerts();
    
    // Mettre en surbrillance le lien actif
    highlightActiveNav();
    
    // Animation d'entrée pour les cartes
    const cartes = document.querySelectorAll('.card, .feature-card, .medecin-card');
    cartes.forEach((carte, index) => {
        carte.style.opacity = '0';
        carte.style.transform = 'translateY(20px)';
        setTimeout(() => {
            carte.style.transition = 'all 0.5s ease';
            carte.style.opacity = '1';
            carte.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    console.log('✅ Scripts principaux chargés');
});
