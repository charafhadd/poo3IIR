/**
 * Fonctionnalités JavaScript améliorées pour la page dossier médical
 * Améliore les fonctionnalités existantes et ajoute de nouvelles
 */

// ============================================
// AMÉLIORATION DES VUES EXISTANTES
// ============================================

/**
 * Améliore la fonction showView existante
 */
function enhanceViewToggle() {
    const viewButtons = document.querySelectorAll('[onclick*="showView"]');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Mettre à jour les boutons actifs
            document.querySelectorAll('.btn-group button').forEach(b => {
                b.classList.remove('active');
                b.classList.add('btn-outline-primary');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline-primary');
        });
    });
}

/**
 * Améliore la fonction filterDossiers existante
 */
function enhanceFilterDossiers() {
    const filterMedecin = document.getElementById('filterMedecin');
    const filterYear = document.getElementById('filterYear');
    
    if (filterMedecin) {
        filterMedecin.addEventListener('change', function() {
            filterDossiers();
            updateDossierCounter();
        });
    }
    
    if (filterYear) {
        filterYear.addEventListener('change', function() {
            filterDossiers();
            updateDossierCounter();
        });
    }
}

/**
 * Met à jour le compteur de dossiers visibles
 */
function updateDossierCounter() {
    const visibleItems = document.querySelectorAll('.dossier-item:not([style*="display: none"])');
    const totalItems = document.querySelectorAll('.dossier-item').length;
    const visibleCount = Array.from(visibleItems).filter(item => {
        return item.style.display !== 'none';
    }).length;
    
    const counter = document.getElementById('dossierCounter');
    if (counter) {
        counter.textContent = `${visibleCount} / ${totalItems}`;
    }
}

// ============================================
// MODAL DE DÉTAILS
// ============================================

/**
 * Crée une modal pour afficher les détails d'un dossier
 * @param {number} dossierId - ID du dossier
 */
function viewDetails(dossierId) {
    // Trouver le dossier correspondant
    const dossierItem = document.querySelector(`[data-dossier-id="${dossierId}"]`);
    if (!dossierItem) {
        // Essayer de trouver par l'ID dans le DOM
        const dossierCard = Array.from(document.querySelectorAll('.dossier-card')).find(card => {
            const btn = card.querySelector(`[onclick*="${dossierId}"]`);
            return btn !== null;
        });
        
        if (!dossierCard) {
            showToast('Dossier non trouvé', 'error');
            return;
        }
        
        showDossierModal(dossierCard);
    } else {
        showDossierModal(dossierItem);
    }
}

/**
 * Affiche une modal avec les détails du dossier
 * @param {HTMLElement} dossierElement - Élément du dossier
 */
function showDossierModal(dossierElement) {
    const medecinName = dossierElement.querySelector('h4')?.textContent || 'Médecin';
    const specialite = dossierElement.querySelector('.badge')?.textContent || '';
    const diagnostic = dossierElement.querySelector('[class*="diagnostic"]')?.textContent || 
                      dossierElement.textContent.match(/Diagnostic[:\s]+(.+?)(?=Traitement|Notes|$)/s)?.[1] || '';
    const traitement = dossierElement.querySelector('[class*="traitement"]')?.textContent || 
                      dossierElement.textContent.match(/Traitement[:\s]+(.+?)(?=Notes|$)/s)?.[1] || '';
    const notes = dossierElement.querySelector('[class*="notes"]')?.textContent || 
                 dossierElement.textContent.match(/Notes[:\s]+(.+?)$/s)?.[1] || '';
    const date = dossierElement.querySelector('h5.text-primary')?.textContent || '';
    
    const modalHtml = `
        <div class="modal fade" id="dossierModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-medical"></i> Détails du dossier médical
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-user-md"></i> Médecin
                            </h6>
                            <p class="mb-1"><strong>${medecinName}</strong></p>
                            <span class="badge bg-info">${specialite}</span>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-calendar"></i> Date de consultation
                            </h6>
                            <p>${date}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-success">
                                <i class="fas fa-stethoscope"></i> Diagnostic
                            </h6>
                            <div class="p-3 bg-light rounded">
                                ${diagnostic || 'Non spécifié'}
                            </div>
                        </div>
                        
                        ${traitement ? `
                        <div class="mb-4">
                            <h6 class="text-warning">
                                <i class="fas fa-pills"></i> Traitement prescrit
                            </h6>
                            <div class="p-3 bg-light rounded">
                                ${traitement}
                            </div>
                        </div>
                        ` : ''}
                        
                        ${notes ? `
                        <div class="mb-4">
                            <h6 class="text-info">
                                <i class="fas fa-notes-medical"></i> Notes du médecin
                            </h6>
                            <div class="p-3 bg-light rounded">
                                ${notes}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="printDossierFromModal()">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Supprimer l'ancienne modal si elle existe
    const oldModal = document.getElementById('dossierModal');
    if (oldModal) {
        oldModal.remove();
    }
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('dossierModal'));
    modal.show();
}

/**
 * Imprime le dossier depuis la modal
 */
function printDossierFromModal() {
    const modal = document.getElementById('dossierModal');
    if (modal) {
        const printContent = modal.querySelector('.modal-body').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Dossier Médical</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                    <style>
                        @media print {
                            body { padding: 20px; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// ============================================
// EXPORT PDF AMÉLIORÉ
// ============================================

/**
 * Exporte le dossier médical en PDF (amélioration de la fonction existante)
 */
function exportToPDF() {
    // Vérifier si jsPDF est disponible
    if (typeof window.jsPDF === 'undefined') {
        // Charger jsPDF depuis CDN
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = function() {
            generatePDF();
        };
        document.head.appendChild(script);
    } else {
        generatePDF();
    }
}

/**
 * Génère le PDF
 */
function generatePDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // En-tête
    doc.setFontSize(18);
    doc.text('Dossier Médical', 105, 20, { align: 'center' });
    
    doc.setFontSize(12);
    const patientName = document.querySelector('.dossier-header p.lead')?.textContent || 'Patient';
    doc.text(`Patient: ${patientName}`, 20, 35);
    doc.text(`Date d'export: ${new Date().toLocaleDateString('fr-FR')}`, 20, 45);
    
    // Contenu des dossiers
    const dossiers = document.querySelectorAll('.dossier-card');
    let yPos = 60;
    
    dossiers.forEach((dossier, index) => {
        if (yPos > 250) {
            doc.addPage();
            yPos = 20;
        }
        
        const medecin = dossier.querySelector('h4')?.textContent || '';
        const date = dossier.querySelector('h5.text-primary')?.textContent || '';
        const diagnostic = dossier.querySelector('[class*="diagnostic"]')?.textContent || '';
        
        doc.setFontSize(14);
        doc.text(`Consultation ${index + 1}`, 20, yPos);
        yPos += 10;
        
        doc.setFontSize(10);
        doc.text(`Médecin: ${medecin}`, 20, yPos);
        yPos += 7;
        doc.text(`Date: ${date}`, 20, yPos);
        yPos += 7;
        doc.text(`Diagnostic: ${diagnostic.substring(0, 150)}...`, 20, yPos);
        yPos += 15;
    });
    
    // Sauvegarder
    doc.save(`dossier-medical-${new Date().toISOString().split('T')[0]}.pdf`);
    showToast('PDF généré avec succès', 'success');
}

// ============================================
// RECHERCHE DANS LES DOSSIERS
// ============================================

/**
 * Initialise la recherche dans les dossiers
 */
function initDossierSearch() {
    const container = document.querySelector('.container.my-5');
    if (!container) return;

    const searchDiv = document.createElement('div');
    searchDiv.className = 'mb-3';
    searchDiv.innerHTML = `
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" id="searchDossier" 
                   placeholder="Rechercher dans les dossiers (médecin, diagnostic, traitement...)">
            <button class="btn btn-outline-secondary" type="button" id="clearDossierSearch">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    const filterCard = document.querySelector('.card.shadow-sm.mb-4');
    if (filterCard) {
        filterCard.insertAdjacentElement('afterend', searchDiv);
    }

    const searchInput = document.getElementById('searchDossier');
    const clearBtn = document.getElementById('clearDossierSearch');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchInDossiers(this.value.toLowerCase());
            updateDossierCounter();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInDossiers('');
            updateDossierCounter();
            searchInput.focus();
        });
    }
}

/**
 * Recherche dans les dossiers
 * @param {string} searchTerm - Terme de recherche
 */
function searchInDossiers(searchTerm) {
    const dossierItems = document.querySelectorAll('.dossier-item');
    
    dossierItems.forEach(item => {
        const cardText = item.textContent.toLowerCase();
        const matches = !searchTerm || cardText.includes(searchTerm);
        
        if (matches) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// ============================================
// STATISTIQUES AVANCÉES
// ============================================

/**
 * Calcule et affiche des statistiques avancées
 */
function initAdvancedStats() {
    const dossiers = document.querySelectorAll('.dossier-card');
    const stats = {
        total: dossiers.length,
        medecins: new Set(),
        dernierConsultation: null,
        premierConsultation: null
    };

    dossiers.forEach(dossier => {
        const medecinId = dossier.closest('.dossier-item')?.getAttribute('data-medecin');
        if (medecinId) {
            stats.medecins.add(medecinId);
        }
        
        const dateText = dossier.querySelector('h5.text-primary')?.textContent || '';
        // Extraire la date si possible
    });

    // Afficher les stats si nécessaire
    const statsContainer = document.querySelector('.stat-box');
    if (statsContainer && stats.medecins.size > 0) {
        // Les stats sont déjà affichées dans le HTML, on peut les mettre à jour dynamiquement
    }
}

// ============================================
// AMÉLIORATION DE L'IMPRESSION
// ============================================

/**
 * Améliore la fonction d'impression
 */
function enhancePrint() {
    // Ajouter un style d'impression personnalisé
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .navbar, .btn, .print-btn, footer { display: none !important; }
            .dossier-card { page-break-inside: avoid; }
            body { background: white; }
        }
    `;
    document.head.appendChild(style);
}

// ============================================
// INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on est sur la page dossier médical
    if (window.location.pathname.includes('dossier_medical')) {
        enhanceViewToggle();
        enhanceFilterDossiers();
        initDossierSearch();
        initAdvancedStats();
        enhancePrint();
        
        // Créer un compteur de dossiers
        const filterCard = document.querySelector('.card.shadow-sm.mb-4');
        if (filterCard) {
            const counterDiv = document.createElement('div');
            counterDiv.className = 'text-muted text-end mb-2';
            counterDiv.id = 'dossierCounter';
            counterDiv.innerHTML = '<small><i class="fas fa-file-medical"></i> <span id="dossierCounter">0</span> dossier(s)</small>';
            filterCard.insertAdjacentElement('beforebegin', counterDiv);
            updateDossierCounter();
        }
        
        // Améliorer la fonction printDossier existante
        const printButtons = document.querySelectorAll('[onclick*="printDossier"]');
        printButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const onclickAttr = this.getAttribute('onclick');
                const dossierId = onclickAttr.match(/\d+/)?.[0];
                if (dossierId) {
                    printDossier(parseInt(dossierId));
                }
            });
        });
        
        console.log('✅ Scripts dossier médical améliorés');
    }
});

// Surcharger la fonction printDossier existante pour amélioration
const originalPrintDossier = window.printDossier;
window.printDossier = function(id) {
    const dossierItem = Array.from(document.querySelectorAll('.dossier-item')).find(item => {
        const btn = item.querySelector(`[onclick*="${id}"]`);
        return btn !== null;
    });
    
    if (dossierItem) {
        const printContent = dossierItem.innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Dossier Médical #${id}</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    } else if (originalPrintDossier) {
        originalPrintDossier(id);
    }
};

