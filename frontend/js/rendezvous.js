/**
 * Fonctionnalités JavaScript pour la page des rendez-vous
 * Gestion des RDV, filtres, et interactions
 */

// ============================================
// FILTRES ET RECHERCHE
// ============================================

/**
 * Initialise les filtres de rendez-vous
 */
function initRdvFilters() {
    const container = document.querySelector('.container.my-5');
    if (!container) return;

    // Créer la barre de filtres
    const filterBar = document.createElement('div');
    filterBar.className = 'card shadow-sm mb-4';
    filterBar.innerHTML = `
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="mb-0">
                        <i class="fas fa-filter text-primary"></i> Filtrer les rendez-vous
                    </h6>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterStatut">
                        <option value="all">Tous les statuts</option>
                        <option value="en_attente">En attente</option>
                        <option value="confirme">Confirmé</option>
                        <option value="annule">Annulé</option>
                        <option value="termine">Terminé</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchRdv" 
                           placeholder="Rechercher par médecin...">
                </div>
            </div>
        </div>
    `;

    const firstElement = container.querySelector('h2, .d-flex');
    if (firstElement) {
        firstElement.insertAdjacentElement('afterend', filterBar);
    } else {
        container.insertBefore(filterBar, container.firstChild);
    }

    // Écouter les changements
    const statutFilter = document.getElementById('filterStatut');
    const searchInput = document.getElementById('searchRdv');

    if (statutFilter) {
        statutFilter.addEventListener('change', function() {
            filterRendezVous(this.value, searchInput?.value || '');
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterRendezVous(statutFilter?.value || 'all', this.value.toLowerCase());
        });
    }
}

/**
 * Filtre les rendez-vous selon les critères
 * @param {string} statut - Statut sélectionné
 * @param {string} searchTerm - Terme de recherche
 */
function filterRendezVous(statut, searchTerm) {
    const rdvCards = document.querySelectorAll('.rdv-card');
    let visibleCount = 0;

    rdvCards.forEach(card => {
        const cardStatut = card.classList.contains('status-en_attente') ? 'en_attente' :
                          card.classList.contains('status-confirme') ? 'confirme' :
                          card.classList.contains('status-annule') ? 'annule' :
                          card.classList.contains('status-termine') ? 'termine' : '';
        
        const cardText = card.textContent.toLowerCase();
        const matchesStatut = statut === 'all' || cardStatut === statut;
        const matchesSearch = !searchTerm || cardText.includes(searchTerm);
        const matches = matchesStatut && matchesSearch;

        if (matches) {
            card.closest('.col-md-6').style.display = '';
            visibleCount++;
            // Animation
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '1';
            }, 10);
        } else {
            card.closest('.col-md-6').style.display = 'none';
        }
    });

    // Afficher message si aucun résultat
    showNoRdvMessage(visibleCount === 0 && (statut !== 'all' || searchTerm.length > 0));
    updateRdvCounter(visibleCount);
}

/**
 * Affiche un message quand aucun RDV n'est trouvé
 * @param {boolean} show - Afficher ou masquer
 */
function showNoRdvMessage(show) {
    let noRdvDiv = document.getElementById('noRdvMessage');
    
    if (show && !noRdvDiv) {
        noRdvDiv = document.createElement('div');
        noRdvDiv.id = 'noRdvMessage';
        noRdvDiv.className = 'alert alert-info text-center mt-4';
        noRdvDiv.innerHTML = `
            <i class="fas fa-calendar-times fa-2x mb-2"></i>
            <h5>Aucun rendez-vous trouvé</h5>
            <p>Essayez de modifier vos critères de recherche.</p>
        `;
        
        const container = document.querySelector('.row.g-3');
        if (container) {
            container.parentElement.appendChild(noRdvDiv);
        }
    } else if (!show && noRdvDiv) {
        noRdvDiv.remove();
    }
}

/**
 * Met à jour le compteur de RDV visibles
 * @param {number} count - Nombre de RDV visibles
 */
function updateRdvCounter(count) {
    const counter = document.getElementById('rdvCounter');
    if (counter) {
        counter.textContent = count;
    }
}

// ============================================
// GESTION DES ANNULATIONS
// ============================================

/**
 * Améliore la confirmation d'annulation de RDV
 */
function initRdvCancellation() {
    const cancelLinks = document.querySelectorAll('a[href*="delete.php"]');
    
    cancelLinks.forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const rdvCard = this.closest('.rdv-card');
            const medecinName = rdvCard?.querySelector('.card-title')?.textContent || 'ce rendez-vous';
            const date = rdvCard?.querySelector('p.ms-4')?.textContent || '';
            
            const confirmed = await confirmAction(
                `Êtes-vous sûr de vouloir annuler votre rendez-vous avec ${medecinName} du ${date} ?`,
                'Annuler le rendez-vous'
            );
            
            if (confirmed) {
                // Afficher un loader
                showLoader(true);
                
                // Rediriger vers la page de suppression
                window.location.href = this.href;
            }
        });
    });
}

// ============================================
// TRI DES RENDEZ-VOUS
// ============================================

/**
 * Initialise le tri des rendez-vous
 */
function initRdvSort() {
    const container = document.querySelector('.container.my-5');
    if (!container) return;

    const sortDiv = document.createElement('div');
    sortDiv.className = 'mb-3 d-flex justify-content-end';
    sortDiv.innerHTML = `
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                    id="sortRdvDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-sort"></i> Trier par
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortRdvDropdown">
                <li><a class="dropdown-item" href="#" data-sort="date-desc">Date (récent)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="date-asc">Date (ancien)</a></li>
                <li><a class="dropdown-item" href="#" data-sort="statut">Statut</a></li>
                <li><a class="dropdown-item" href="#" data-sort="medecin">Médecin</a></li>
            </ul>
        </div>
    `;

    const filterBar = document.getElementById('filterStatut')?.closest('.card');
    if (filterBar) {
        filterBar.insertAdjacentElement('afterend', sortDiv);
    }

    // Écouter les clics
    const sortLinks = sortDiv.querySelectorAll('.dropdown-item');
    sortLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sortType = this.getAttribute('data-sort');
            sortRendezVous(sortType);
            
            const dropdownBtn = document.getElementById('sortRdvDropdown');
            dropdownBtn.innerHTML = `<i class="fas fa-sort"></i> ${this.textContent}`;
        });
    });
}

/**
 * Trie les rendez-vous
 * @param {string} sortType - Type de tri
 */
function sortRendezVous(sortType) {
    const container = document.querySelector('.row.g-3');
    if (!container) return;

    const cards = Array.from(container.querySelectorAll('.col-md-6'));
    
    cards.sort((a, b) => {
        const cardA = a.querySelector('.rdv-card');
        const cardB = b.querySelector('.rdv-card');
        
        switch(sortType) {
            case 'date-desc':
                const dateA = new Date(cardA.querySelector('[data-date]')?.getAttribute('data-date') || 
                                    cardA.textContent.match(/\d{2}\/\d{2}\/\d{4}/)?.[0] || '');
                const dateB = new Date(cardB.querySelector('[data-date]')?.getAttribute('data-date') || 
                                    cardB.textContent.match(/\d{2}\/\d{2}\/\d{4}/)?.[0] || '');
                return dateB - dateA;
                
            case 'date-asc':
                const dateAAsc = new Date(cardA.querySelector('[data-date]')?.getAttribute('data-date') || 
                                         cardA.textContent.match(/\d{2}\/\d{2}\/\d{4}/)?.[0] || '');
                const dateBAsc = new Date(cardB.querySelector('[data-date]')?.getAttribute('data-date') || 
                                         cardB.textContent.match(/\d{2}\/\d{2}\/\d{4}/)?.[0] || '');
                return dateAAsc - dateBAsc;
                
            case 'statut':
                const statutOrder = { 'en_attente': 1, 'confirme': 2, 'termine': 3, 'annule': 4 };
                const statutA = cardA.classList.contains('status-en_attente') ? 'en_attente' :
                               cardA.classList.contains('status-confirme') ? 'confirme' :
                               cardA.classList.contains('status-termine') ? 'termine' : 'annule';
                const statutB = cardB.classList.contains('status-en_attente') ? 'en_attente' :
                               cardB.classList.contains('status-confirme') ? 'confirme' :
                               cardB.classList.contains('status-termine') ? 'termine' : 'annule';
                return (statutOrder[statutA] || 0) - (statutOrder[statutB] || 0);
                
            case 'medecin':
                const medecinA = cardA.querySelector('.card-title')?.textContent || '';
                const medecinB = cardB.querySelector('.card-title')?.textContent || '';
                return medecinA.localeCompare(medecinB, 'fr');
                
            default:
                return 0;
        }
    });

    // Réorganiser dans le DOM
    cards.forEach(card => container.appendChild(card));
    
    // Animation
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        setTimeout(() => {
            card.style.transition = 'opacity 0.3s ease';
            card.style.opacity = '1';
        }, index * 50);
    });
}

// ============================================
// STATISTIQUES DYNAMIQUES
// ============================================

/**
 * Met à jour les statistiques en temps réel
 */
function updateStatistics() {
    const rdvCards = document.querySelectorAll('.rdv-card');
    const stats = {
        total: rdvCards.length,
        en_attente: 0,
        confirme: 0,
        annule: 0,
        termine: 0
    };

    rdvCards.forEach(card => {
        if (card.classList.contains('status-en_attente')) stats.en_attente++;
        else if (card.classList.contains('status-confirme')) stats.confirme++;
        else if (card.classList.contains('status-annule')) stats.annule++;
        else if (card.classList.contains('status-termine')) stats.termine++;
    });

    // Mettre à jour les éléments de statistiques si ils existent
    const totalEl = document.querySelector('[data-stat="total"]');
    const enAttenteEl = document.querySelector('[data-stat="en_attente"]');
    const confirmeEl = document.querySelector('[data-stat="confirme"]');
    const termineEl = document.querySelector('[data-stat="termine"]');

    if (totalEl) totalEl.textContent = stats.total;
    if (enAttenteEl) enAttenteEl.textContent = stats.en_attente;
    if (confirmeEl) confirmeEl.textContent = stats.confirme;
    if (termineEl) termineEl.textContent = stats.termine;
}

// ============================================
// EXPORT ET IMPRESSION
// ============================================

/**
 * Exporte les rendez-vous au format CSV
 */
function exportRdvToCSV() {
    const rdvCards = document.querySelectorAll('.rdv-card');
    const csvData = [];
    
    csvData.push(['Date', 'Heure', 'Médecin', 'Spécialité', 'Statut', 'Motif']);
    
    rdvCards.forEach(card => {
        const date = card.querySelector('p.ms-4')?.textContent || '';
        const heure = card.textContent.match(/\d{2}:\d{2}/)?.[0] || '';
        const medecin = card.querySelector('.card-title')?.textContent.replace('Dr. ', '') || '';
        const specialite = card.querySelector('.text-muted')?.textContent || '';
        const statut = card.querySelector('.badge')?.textContent || '';
        const motif = card.textContent.match(/Motif: (.+?)(?:\n|$)/)?.[1] || '';
        
        csvData.push([date, heure, medecin, specialite, statut, motif]);
    });
    
    const csvContent = csvData.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `rendez-vous-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    
    showToast('Export CSV réussi', 'success');
}

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on est sur la page des rendez-vous
    if (window.location.pathname.includes('rendezvous')) {
        initRdvFilters();
        initRdvCancellation();
        initRdvSort();
        updateStatistics();
        
        console.log('✅ Scripts rendez-vous initialisés');
    }
});

