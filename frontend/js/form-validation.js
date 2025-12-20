/**
 * Validation des formulaires côté client
 * Améliore l'expérience utilisateur avec validation en temps réel
 */

// ============================================
// VALIDATION GÉNÉRALE
// ============================================

/**
 * Valide un email (doit se terminer par @gmail.com)
 * @param {string} email - Email à valider
 * @returns {boolean}
 */
function isValidEmail(email) {
    // Vérifier que l'email se termine par @gmail.com
    return email.toLowerCase().endsWith('@gmail.com') && email.includes('@');
}

/**
 * Valide un numéro de téléphone (format français/marocain)
 * @param {string} telephone - Numéro de téléphone
 * @returns {boolean}
 */
function isValidPhone(telephone) {
    // Accepte les formats: 0612345678, +212612345678, 06 12 34 56 78
    const regexTelephone = /^(\+212|0)[5-7]\d{8}$/;
    const telephoneNettoye = telephone.replace(/\s+/g, '');
    return regexTelephone.test(telephoneNettoye);
}

/**
 * Valide un mot de passe (minimum 8 caractères)
 * @param {string} motDePasse - Mot de passe
 * @returns {boolean}
 */
function isValidPassword(motDePasse) {
    return motDePasse.length >= 8;
}

/**
 * Valide une date (doit être dans le futur pour les RDV)
 * @param {string} chaineDate - Date à valider
 * @returns {boolean}
 */
function isValidDate(chaineDate) {
    if (!chaineDate) return false;
    const date = new Date(chaineDate);
    const aujourdhui = new Date();
    aujourdhui.setHours(0, 0, 0, 0);
    return date >= aujourdhui;
}

/**
 * Affiche un message d'erreur pour un champ
 * @param {HTMLElement} champ - Champ de formulaire
 * @param {string} message - Message d'erreur
 */
function showFieldError(champ, message) {
    // Supprimer l'ancien message d'erreur
    const erreurExistante = champ.parentElement.querySelector('.invalid-feedback');
    if (erreurExistante) {
        erreurExistante.remove();
    }

    // Ajouter le nouveau message
    const divErreur = document.createElement('div');
    divErreur.className = 'invalid-feedback';
    divErreur.textContent = message;
    champ.parentElement.appendChild(divErreur);
    champ.classList.add('is-invalid');
    champ.classList.remove('is-valid');
}

/**
 * Affiche un message de succès pour un champ
 * @param {HTMLElement} champ - Champ de formulaire
 */
function showFieldSuccess(champ) {
    champ.classList.remove('is-invalid');
    champ.classList.add('is-valid');
    const erreurExistante = champ.parentElement.querySelector('.invalid-feedback');
    if (erreurExistante) {
        erreurExistante.remove();
    }
}

/**
 * Réinitialise la validation d'un champ
 * @param {HTMLElement} champ - Champ de formulaire
 */
function resetFieldValidation(champ) {
    champ.classList.remove('is-invalid', 'is-valid');
    const erreurExistante = champ.parentElement.querySelector('.invalid-feedback');
    if (erreurExistante) {
        erreurExistante.remove();
    }
}

// ============================================
// VALIDATION FORMULAIRE INSCRIPTION
// ============================================

function initSignupValidation() {
    const formulaire = document.querySelector('form[action*="signup"]');
    if (!formulaire) return;

    const champNom = formulaire.querySelector('input[name="nom"]');
    const champPrenom = formulaire.querySelector('input[name="prenom"]');
    const champEmail = formulaire.querySelector('input[name="email"]');
    const champTelephone = formulaire.querySelector('input[name="telephone"]');
    const champMotDePasse = formulaire.querySelector('input[name="password"]');
    const champConfirmationMotDePasse = formulaire.querySelector('input[name="confirm_password"]');
    const champDateNaissance = formulaire.querySelector('input[name="date_naissance"]');

    // Validation du nom
    if (champNom) {
        champNom.addEventListener('blur', function() {
            if (this.value.trim().length < 2) {
                showFieldError(this, 'Le nom doit contenir au moins 2 caractères');
            } else {
                showFieldSuccess(this);
            }
        });
    }

    // Validation du prénom
    if (champPrenom) {
        champPrenom.addEventListener('blur', function() {
            if (this.value.trim().length < 2) {
                showFieldError(this, 'Le prénom doit contenir au moins 2 caractères');
            } else {
                showFieldSuccess(this);
            }
        });
    }

    // Validation de l'email
    if (champEmail) {
        champEmail.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showFieldError(this, 'L\'email est obligatoire');
            } else if (!isValidEmail(this.value)) {
                showFieldError(this, 'L\'email doit se terminer par @gmail.com');
            } else {
                showFieldSuccess(this);
            }
        });
    }

    // Validation du téléphone (optionnel)
    if (champTelephone) {
        champTelephone.addEventListener('blur', function() {
            if (this.value.trim() && !isValidPhone(this.value)) {
                showFieldError(this, 'Format de téléphone invalide (ex: 0612345678)');
            } else if (this.value.trim()) {
                showFieldSuccess(this);
            } else {
                resetFieldValidation(this);
            }
        });
    }

    // Validation du mot de passe
    if (champMotDePasse) {
        champMotDePasse.addEventListener('input', function() {
            if (this.value.length > 0 && !isValidPassword(this.value)) {
                showFieldError(this, 'Le mot de passe doit contenir au moins 8 caractères');
            } else if (this.value.length > 0) {
                showFieldSuccess(this);
            }
        });
    }

    // Validation de la confirmation du mot de passe
    if (champConfirmationMotDePasse && champMotDePasse) {
        champConfirmationMotDePasse.addEventListener('blur', function() {
            if (this.value !== champMotDePasse.value) {
                showFieldError(this, 'Les mots de passe ne correspondent pas');
            } else if (this.value.length > 0) {
                showFieldSuccess(this);
            }
        });

        champMotDePasse.addEventListener('input', function() {
            if (champConfirmationMotDePasse.value && champConfirmationMotDePasse.value !== this.value) {
                showFieldError(champConfirmationMotDePasse, 'Les mots de passe ne correspondent pas');
            } else if (champConfirmationMotDePasse.value) {
                showFieldSuccess(champConfirmationMotDePasse);
            }
        });
    }

    // Validation de la date de naissance
    if (champDateNaissance) {
        champDateNaissance.addEventListener('change', function() {
            if (this.value) {
                const dateNaissance = new Date(this.value);
                const aujourdhui = new Date();
                const age = aujourdhui.getFullYear() - dateNaissance.getFullYear();
                
                if (age < 0 || age > 120) {
                    showFieldError(this, 'Date de naissance invalide');
                } else {
                    showFieldSuccess(this);
                }
            }
        });
    }

    // Validation avant soumission
    formulaire.addEventListener('submit', function(e) {
        let estValide = true;

        // Vérifier tous les champs obligatoires
        if (champNom && champNom.value.trim().length < 2) {
            showFieldError(champNom, 'Le nom est obligatoire');
            estValide = false;
        }

        if (champPrenom && champPrenom.value.trim().length < 2) {
            showFieldError(champPrenom, 'Le prénom est obligatoire');
            estValide = false;
        }

        if (champEmail && !isValidEmail(champEmail.value)) {
            showFieldError(champEmail, 'L\'email doit se terminer par @gmail.com');
            estValide = false;
        }

        if (champMotDePasse && !isValidPassword(champMotDePasse.value)) {
            showFieldError(champMotDePasse, 'Le mot de passe doit contenir au moins 8 caractères');
            estValide = false;
        }

        if (champConfirmationMotDePasse && champConfirmationMotDePasse.value !== champMotDePasse.value) {
            showFieldError(champConfirmationMotDePasse, 'Les mots de passe ne correspondent pas');
            estValide = false;
        }

        if (champTelephone && champTelephone.value.trim() && !isValidPhone(champTelephone.value)) {
            showFieldError(champTelephone, 'Format de téléphone invalide');
            estValide = false;
        }

        if (!estValide) {
            e.preventDefault();
            showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
            smoothScrollTo(formulaire.querySelector('.is-invalid'));
        }
    });
}

// ============================================
// VALIDATION FORMULAIRE CONNEXION
// ============================================

function initLoginValidation() {
    const formulaire = document.querySelector('form[method="post"]');
    if (!formulaire || !formulaire.querySelector('input[name="email"]')) return;

    const champEmail = formulaire.querySelector('input[name="email"]');
    const champMotDePasse = formulaire.querySelector('input[name="password"]');

    if (champEmail) {
        champEmail.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showFieldError(this, 'L\'email est obligatoire');
            } else if (!isValidEmail(this.value)) {
                showFieldError(this, 'L\'email doit se terminer par @gmail.com');
            } else {
                showFieldSuccess(this);
            }
        });
    }

    if (champMotDePasse) {
        champMotDePasse.addEventListener('blur', function() {
            if (this.value.length === 0) {
                showFieldError(this, 'Le mot de passe est obligatoire');
            } else {
                showFieldSuccess(this);
            }
        });
    }

    formulaire.addEventListener('submit', function(e) {
        let estValide = true;

        if (!isValidEmail(champEmail.value)) {
            showFieldError(champEmail, 'L\'email doit se terminer par @gmail.com');
            estValide = false;
        }

        if (champMotDePasse.value.length === 0) {
            showFieldError(champMotDePasse, 'Le mot de passe est obligatoire');
            estValide = false;
        }

        if (!estValide) {
            e.preventDefault();
            showToast('Veuillez remplir correctement tous les champs', 'error');
        }
    });
}

// ============================================
// VALIDATION FORMULAIRE PRISE DE RDV
// ============================================

function initRdvValidation() {
    const formulaire = document.querySelector('form[action*="prendre_rdv"]');
    if (!formulaire) return;

    const champMedecin = formulaire.querySelector('select[name="medecin_id"]');
    const champDate = formulaire.querySelector('input[name="date_rdv"]');
    const champHeure = formulaire.querySelector('select[name="heure_rdv"]');
    const champMotif = formulaire.querySelector('textarea[name="motif"]');

    // Définir la date minimale (aujourd'hui)
    if (champDate) {
        const aujourdhui = new Date().toISOString().split('T')[0];
        champDate.setAttribute('min', aujourdhui);

        champDate.addEventListener('change', function() {
            const dateSelectionnee = new Date(this.value);
            const aujourdhui = new Date();
            aujourdhui.setHours(0, 0, 0, 0);

            if (dateSelectionnee < aujourdhui) {
                showFieldError(this, 'La date doit être aujourd\'hui ou dans le futur');
            } else {
                showFieldSuccess(this);
                // Vérifier la disponibilité si médecin et heure sont sélectionnés
                if (champMedecin && champMedecin.value && champHeure && champHeure.value) {
                    verifierDisponibilite(champMedecin.value, this.value, champHeure.value);
                }
            }
        });
    }

    // Validation du médecin
    if (champMedecin) {
        champMedecin.addEventListener('change', function() {
            if (!this.value) {
                showFieldError(this, 'Veuillez sélectionner un médecin');
            } else {
                showFieldSuccess(this);
                // Vérifier la disponibilité si date et heure sont sélectionnés
                if (champDate && champDate.value && champHeure && champHeure.value) {
                    verifierDisponibilite(this.value, champDate.value, champHeure.value);
                }
            }
        });
    }

    // Validation de l'heure
    if (champHeure) {
        champHeure.addEventListener('change', function() {
            if (!this.value) {
                showFieldError(this, 'Veuillez sélectionner une heure');
            } else {
                showFieldSuccess(this);
                // Vérifier la disponibilité si médecin et date sont sélectionnés
                if (champMedecin && champMedecin.value && champDate && champDate.value) {
                    verifierDisponibilite(champMedecin.value, champDate.value, this.value);
                }
            }
        });
    }

    // Validation avant soumission
    formulaire.addEventListener('submit', function(e) {
        let estValide = true;

        if (!champMedecin || !champMedecin.value) {
            showFieldError(champMedecin, 'Veuillez sélectionner un médecin');
            estValide = false;
        }

        if (!champDate || !isValidDate(champDate.value)) {
            showFieldError(champDate, 'Veuillez sélectionner une date valide');
            estValide = false;
        }

        if (!champHeure || !champHeure.value) {
            showFieldError(champHeure, 'Veuillez sélectionner une heure');
            estValide = false;
        }

        if (!estValide) {
            e.preventDefault();
            showToast('Veuillez remplir tous les champs obligatoires', 'error');
            smoothScrollTo(formulaire.querySelector('.is-invalid'));
        }
    });
}

/**
 * Vérifie la disponibilité d'un créneau (simulation côté client)
 * Note: Une vraie vérification nécessiterait un appel AJAX au serveur
 */
function verifierDisponibilite(idMedecin, date, heure) {
    // Cette fonction devrait faire un appel AJAX pour vérifier la disponibilité réelle
    // Pour l'instant, on simule juste une validation visuelle
    const champDate = document.querySelector('input[name="date_rdv"]');
    const champHeure = document.querySelector('select[name="heure_rdv"]');
    
    if (champDate && champHeure) {
        // Vérifier si c'est un jour passé
        const dateSelectionnee = new Date(date);
        const aujourdhui = new Date();
        aujourdhui.setHours(0, 0, 0, 0);
        
        if (dateSelectionnee < aujourdhui) {
            showFieldError(champDate, 'Vous ne pouvez pas prendre RDV pour une date passée');
            return false;
        }
        
        // Vérifier les heures de bureau (8h-18h)
        const heureSelectionnee = parseInt(heure.split(':')[0]);
        if (heureSelectionnee < 8 || heureSelectionnee >= 18) {
            showFieldError(champHeure, 'Les rendez-vous sont disponibles entre 8h et 18h');
            return false;
        }
    }
    
    return true;
}

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Détecter le type de formulaire et initialiser la validation appropriée
    const cheminActuel = window.location.pathname;
    
    if (cheminActuel.includes('signup')) {
        initSignupValidation();
    } else if (cheminActuel.includes('login')) {
        initLoginValidation();
    } else if (cheminActuel.includes('prendre_rdv')) {
        initRdvValidation();
    }
    
    console.log('✅ Validation des formulaires initialisée');
});
