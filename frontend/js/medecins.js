/**
 * Fonctionnalités JavaScript pour la page des médecins
 * Recherche, filtres et interactions dynamiques
 */

// ============================================
// RECHERCHE ET FILTRES
// ============================================

let tousLesMedecins = [];

/**
 * Initialise la fonctionnalité de recherche
 */
function initSearch() {
    const champRecherche = document.getElementById('searchMedecin');
    if (!champRecherche) return;

    // Créer le champ de recherche s'il n'existe pas
    if (!champRecherche) {
        const conteneur = document.querySelector('.container.my-5');
        if (conteneur) {
            const divRecherche = document.createElement('div');
            divRecherche.className = 'mb-4';
            divRecherche.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchMedecin" 
                           placeholder="Rechercher un médecin par nom, prénom ou spécialité...">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            conteneur.insertBefore(divRecherche, conteneur.firstChild);
        }
    }

    // Stocker tous les médecins au chargement
    const cartesMedecins = document.querySelectorAll('.medecin-card');
    tousLesMedecins = Array.from(cartesMedecins).map(carte => ({
        element: carte,
        texte: carte.textContent.toLowerCase(),
        specialite: carte.querySelector('.specialite-badge')?.textContent.toLowerCase() || ''
    }));

    // Écouter les changements dans le champ de recherche
    const champRechercheFinal = document.getElementById('searchMedecin');
    const boutonEffacer = document.getElementById('clearSearch');

    if (champRechercheFinal) {
        champRechercheFinal.addEventListener('input', function() {
            filterMedecins(this.value.toLowerCase());
        });

        // Recherche avec délai (debounce)
        let delaiRecherche;
        champRechercheFinal.addEventListener('input', function() {
            clearTimeout(delaiRecherche);
            delaiRecherche = setTimeout(() => {
                filterMedecins(this.value.toLowerCase());
            }, 300);
        });
    }

    if (boutonEffacer) {
        boutonEffacer.addEventListener('click', function() {
            champRechercheFinal.value = '';
            filterMedecins('');
            champRechercheFinal.focus();
        });
    }
}

/**
 * Filtre les médecins selon le terme de recherche
 * @param {string} searchTerm - Terme de recherche
 */
function filterMedecins(termeRecherche) {
    const cartesMedecins = document.querySelectorAll('.medecin-card');
    let nombreVisible = 0;

    cartesMedecins.forEach(carte => {
        const texteCarte = carte.textContent.toLowerCase();
        const correspond = !termeRecherche || texteCarte.includes(termeRecherche);

        if (correspond) {
            carte.closest('.col-md-6, .col-lg-4').style.display = '';
            nombreVisible++;
            // Animation d'apparition
            carte.style.opacity = '0';
            setTimeout(() => {
                carte.style.transition = 'opacity 0.3s ease';
                carte.style.opacity = '1';
            }, 10);
        } else {
            carte.closest('.col-md-6, .col-lg-4').style.display = 'none';
        }
    });

    // Afficher un message si aucun résultat
    afficherMessageAucunResultat(nombreVisible === 0 && termeRecherche.length > 0);
}

/**
 * Affiche un message quand aucun médecin n'est trouvé
 * @param {boolean} show - Afficher ou masquer le message
 */
function afficherMessageAucunResultat(afficher) {
    let divAucunResultat = document.getElementById('noResultsMessage');
    
    if (afficher && !divAucunResultat) {
        divAucunResultat = document.createElement('div');
        divAucunResultat.id = 'noResultsMessage';
        divAucunResultat.className = 'alert alert-info text-center mt-4';
        divAucunResultat.innerHTML = `
            <i class="fas fa-search fa-2x mb-2"></i>
            <h5>Aucun médecin trouvé</h5>
            <p>Essayez avec d'autres mots-clés ou vérifiez votre orthographe.</p>
        `;
        
        const conteneur = document.querySelector('.row.g-4');
        if (conteneur) {
            conteneur.parentElement.appendChild(divAucunResultat);
        }
    } else if (!afficher && divAucunResultat) {
        divAucunResultat.remove();
    }
}

// ============================================
// FILTRES PAR SPÉCIALITÉ
// ============================================

/**
 * Initialise les filtres par spécialité
 */
function initSpecialiteFilter() {
    // Récupérer toutes les spécialités uniques
    const badgesSpecialites = document.querySelectorAll('.specialite-badge');
    const specialites = new Set();
    
    badgesSpecialites.forEach(badge => {
        specialites.add(badge.textContent.trim());
    });

    if (specialites.size === 0) return;

    // Créer le filtre de spécialité
    const conteneur = document.querySelector('.container.my-5');
    if (conteneur) {
        const divFiltre = document.createElement('div');
        divFiltre.className = 'mb-4';
        divFiltre.innerHTML = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter text-primary"></i> Filtrer par spécialité
                    </h6>
                    <div class="btn-group flex-wrap" role="group" id="specialiteFilter">
                        <button type="button" class="btn btn-outline-primary active" data-specialite="all">
                            Toutes
                        </button>
                        ${Array.from(specialites).map(spec => `
                            <button type="button" class="btn btn-outline-primary" data-specialite="${spec}">
                                ${spec}
                            </button>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        
        const premierEnfant = conteneur.querySelector('.text-center');
        if (premierEnfant) {
            premierEnfant.insertAdjacentElement('afterend', divFiltre);
        } else {
            conteneur.insertBefore(divFiltre, conteneur.firstChild);
        }

        // Ajouter les écouteurs d'événements
        const boutonsFiltre = document.querySelectorAll('#specialiteFilter button');
        boutonsFiltre.forEach(bouton => {
            bouton.addEventListener('click', function() {
                // Mettre à jour l'état actif
                boutonsFiltre.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filtrer les médecins
                const specialiteSelectionnee = this.getAttribute('data-specialite');
                filterBySpecialite(specialiteSelectionnee);
            });
        });
    }
}

/**
 * Filtre les médecins par spécialité
 * @param {string} specialite - Spécialité sélectionnée ('all' pour tous)
 */
function filterBySpecialite(specialite) {
    const cartesMedecins = document.querySelectorAll('.medecin-card');
    let nombreVisible = 0;

    cartesMedecins.forEach(carte => {
        const specialiteCarte = carte.querySelector('.specialite-badge')?.textContent.trim() || '';
        const correspond = specialite === 'all' || specialiteCarte === specialite;

        if (correspond) {
            carte.closest('.col-md-6, .col-lg-4').style.display = '';
            nombreVisible++;
        } else {
            carte.closest('.col-md-6, .col-lg-4').style.display = 'none';
        }
    });

    // Mettre à jour le message de recherche si nécessaire
    const champRecherche = document.getElementById('searchMedecin');
    if (champRecherche && champRecherche.value) {
        filterMedecins(champRecherche.value.toLowerCase());
    }
}

// ============================================
// TRI DES MÉDECINS
// ============================================

/**
 * Initialise le tri des médecins
 */
function initSort() {
    const conteneur = document.querySelector('.container.my-5');
    if (!conteneur) return;

    const divTri = document.createElement('div');
    divTri.className = 'mb-3 d-flex justify-content-end';
    divTri.innerHTML = `
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                    id="sortDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-sort"></i> Trier par
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item" href="#" data-sort="nom">Nom (A-Z)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="nom-desc">Nom (Z-A)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="specialite">Spécialité</a></li>
                <li><a class="dropdown-item" href="#" data-sort="tarif">Tarif (croissant)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="tarif-desc">Tarif (décroissant)</a></li>
            </ul>
        </div>
    `;

    const premiereLigne = conteneur.querySelector('.row.g-4');
    if (premiereLigne) {
        premiereLigne.insertAdjacentElement('beforebegin', divTri);
    }

    // Ajouter les écouteurs d'événements
    const liensTri = divTri.querySelectorAll('.dropdown-item');
    liensTri.forEach(lien => {
        lien.addEventListener('click', function(e) {
            e.preventDefault();
            const typeTri = this.getAttribute('data-sort');
            sortMedecins(typeTri);
            
            // Mettre à jour le texte du bouton
            const boutonDropdown = document.getElementById('sortDropdown');
            boutonDropdown.innerHTML = `<i class="fas fa-sort"></i> ${this.textContent}`;
        });
    });
}

/**
 * Trie les médecins selon le critère sélectionné
 * @param {string} sortType - Type de tri
 */
function sortMedecins(typeTri) {
    const conteneur = document.querySelector('.row.g-4');
    if (!conteneur) return;

    const cartes = Array.from(conteneur.querySelectorAll('.col-md-6, .col-lg-4'));
    
    cartes.sort((a, b) => {
        const carteA = a.querySelector('.medecin-card');
        const carteB = b.querySelector('.medecin-card');
        
        switch(typeTri) {
            case 'nom':
                const nomA = carteA.querySelector('.card-title')?.textContent || '';
                const nomB = carteB.querySelector('.card-title')?.textContent || '';
                return nomA.localeCompare(nomB, 'fr');
                
            case 'nom-desc':
                const nomADesc = carteA.querySelector('.card-title')?.textContent || '';
                const nomBDesc = carteB.querySelector('.card-title')?.textContent || '';
                return nomBDesc.localeCompare(nomADesc, 'fr');
                
            case 'specialite':
                const specA = carteA.querySelector('.specialite-badge')?.textContent || '';
                const specB = carteB.querySelector('.specialite-badge')?.textContent || '';
                return specA.localeCompare(specB, 'fr');
                
            case 'tarif':
                const tarifA = parseFloat(carteA.textContent.match(/[\d.]+(?=\s*MAD)/)?.[0] || 0);
                const tarifB = parseFloat(carteB.textContent.match(/[\d.]+(?=\s*MAD)/)?.[0] || 0);
                return tarifA - tarifB;
                
            case 'tarif-desc':
                const tarifADesc = parseFloat(carteA.textContent.match(/[\d.]+(?=\s*MAD)/)?.[0] || 0);
                const tarifBDesc = parseFloat(carteB.textContent.match(/[\d.]+(?=\s*MAD)/)?.[0] || 0);
                return tarifBDesc - tarifADesc;
                
            default:
                return 0;
        }
    });

    // Réorganiser les cartes dans le DOM
    cartes.forEach(carte => conteneur.appendChild(carte));
    
    // Animation de réorganisation
    cartes.forEach((carte, index) => {
        carte.style.opacity = '0';
        carte.style.transform = 'translateY(20px)';
        setTimeout(() => {
            carte.style.transition = 'all 0.3s ease';
            carte.style.opacity = '1';
            carte.style.transform = 'translateY(0)';
        }, index * 50);
    });
}

// ============================================
// ANIMATIONS ET INTERACTIONS
// ============================================

/**
 * Ajoute des animations aux cartes de médecins
 */
function initCardAnimations() {
    const cartes = document.querySelectorAll('.medecin-card');
    
    cartes.forEach((carte, index) => {
        // Animation au survol
        carte.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        carte.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });

        // Animation d'entrée
        setTimeout(() => {
            carte.style.opacity = '0';
            carte.style.transform = 'translateY(30px)';
            carte.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                carte.style.opacity = '1';
                carte.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
}

// ============================================
// COMPTEUR DE MÉDECINS
// ============================================

/**
 * Affiche un compteur de médecins visibles
 */
function initMedecinCounter() {
    const conteneur = document.querySelector('.container.my-5');
    if (!conteneur) return;

    const divCompteur = document.createElement('div');
    divCompteur.className = 'mb-3 text-muted';
    divCompteur.id = 'medecinCounter';
    divCompteur.innerHTML = '<i class="fas fa-user-md"></i> <span id="counterText">0</span> médecin(s) trouvé(s)';
    
    const premiereLigne = conteneur.querySelector('.row.g-4');
    if (premiereLigne) {
        premiereLigne.insertAdjacentElement('beforebegin', divCompteur);
    }

    mettreAJourCompteurMedecins();
}

/**
 * Met à jour le compteur de médecins
 */
function mettreAJourCompteurMedecins() {
    const texteCompteur = document.getElementById('counterText');
    if (!texteCompteur) return;

    const cartesVisibles = document.querySelectorAll('.medecin-card:not([style*="display: none"])');
    const totalCartes = document.querySelectorAll('.medecin-card').length;
    
    const nombreVisible = Array.from(cartesVisibles).filter(carte => {
        const parent = carte.closest('.col-md-6, .col-lg-4');
        return parent && parent.style.display !== 'none';
    }).length;

    texteCompteur.textContent = nombreVisible;
    
    // Mettre à jour le style selon le nombre
    const divCompteur = document.getElementById('medecinCounter');
    if (divCompteur) {
        if (nombreVisible === 0) {
            divCompteur.classList.add('text-danger');
            divCompteur.classList.remove('text-muted');
        } else {
            divCompteur.classList.remove('text-danger');
            divCompteur.classList.add('text-muted');
        }
    }
}

// Observer les changements pour mettre à jour le compteur
const observateur = new MutationObserver(() => {
    mettreAJourCompteurMedecins();
});

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on est sur la page des médecins
    if (window.location.pathname.includes('medecins')) {
        initSearch();
        initSpecialiteFilter();
        initSort();
        initCardAnimations();
        initMedecinCounter();
        
        // Observer les changements dans le DOM pour mettre à jour le compteur
        const conteneur = document.querySelector('.row.g-4');
        if (conteneur) {
            observateur.observe(conteneur, { childList: true, subtree: true, attributes: true });
        }
        
        console.log('✅ Scripts médecins initialisés');
    }
});

