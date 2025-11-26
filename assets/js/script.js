/**
 * Scripts JavaScript pour l'application Expression du Besoin
 */

// Configuration globale
const App = {
    config: {
        animationDuration: 300,
        autoHideAlertDelay: 5000,
        searchDelay: 500
    },
    
    // Initialisation de l'application
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.setupAnimations();
        this.initializeTooltips();
        this.setupFormValidation();
        console.log('Application Expression du Besoin initialisée');
    },
    
    // Configuration des écouteurs d'événements
    setupEventListeners() {
        // Gestion des confirmations de suppression
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
                this.handleDeleteConfirmation(e);
            }
        });
        
        // Gestion de la recherche en temps réel
        const searchInputs = document.querySelectorAll('[data-live-search]');
        searchInputs.forEach(input => {
            let searchTimeout;
            input.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performLiveSearch(e.target);
                }, this.config.searchDelay);
            });
        });
        
        // Gestion du changement de vue (table/cartes)
        document.addEventListener('click', (e) => {
            if (e.target.hasAttribute('data-view-toggle')) {
                this.toggleView(e.target.getAttribute('data-view-toggle'));
            }
        });
        
        // Sauvegarde automatique des formulaires
        const forms = document.querySelectorAll('[data-auto-save]');
        forms.forEach(form => {
            this.setupAutoSave(form);
        });
    },
    
    // Initialisation des composants
    initializeComponents() {
        this.initializeTooltips();
        this.initializePopovers();
        this.setupCardAnimations();
        this.initializeDataTables();
        this.setupProgressBars();
    },
    
    // Animations au chargement
    setupAnimations() {
        // Animation des cartes
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = `opacity ${this.config.animationDuration}ms ease, transform ${this.config.animationDuration}ms ease`;
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
        
        // Animation des boutons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                if (!button.disabled) {
                    button.style.transform = 'translateY(-2px)';
                }
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
            });
        });
    },
    
    // Initialisation des tooltips Bootstrap
    initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });
    },
    
    // Initialisation des popovers Bootstrap
    initializePopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(popoverTriggerEl => {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'hover focus'
            });
        });
    },
    
    // Configuration des animations de cartes
    setupCardAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
    },
    
    // Gestion de la confirmation de suppression
    handleDeleteConfirmation(e) {
        e.preventDefault();
        
        const element = e.target.closest('.btn-delete');
        const href = element.getAttribute('href');
        const confirmMessage = element.getAttribute('data-confirm') || 
                              'Êtes-vous sûr de vouloir supprimer cet élément ?';
        
        // Créer une modal de confirmation personnalisée
        this.showConfirmModal(confirmMessage, () => {
            window.location.href = href;
        });
    },
    
    // Modal de confirmation personnalisée
    showConfirmModal(message, onConfirm) {
        const modalHTML = `
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x me-1"></i> Annuler
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">
                                <i class="bi bi-trash me-1"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Supprimer la modal existante si elle existe
        const existingModal = document.getElementById('confirmModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Ajouter la nouvelle modal
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        
        document.getElementById('confirmDelete').addEventListener('click', () => {
            modal.hide();
            onConfirm();
        });
        
        modal.show();
        
        // Nettoyer après fermeture
        document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
            document.getElementById('confirmModal').remove();
        });
    },
    
    // Validation des formulaires
    setupFormValidation() {
        const forms = document.querySelectorAll('.needs-validation, [data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
            
            // Validation en temps réel
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        });
    },
    
    // Validation d'un champ individuel
    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        
        // Supprimer les classes de validation précédentes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Validation des champs obligatoires
        if (required && !value) {
            field.classList.add('is-invalid');
            this.setFieldError(field, 'Ce champ est obligatoire.');
            return false;
        }
        
        // Validation selon le type
        switch (type) {
            case 'email':
                if (value && !this.isValidEmail(value)) {
                    field.classList.add('is-invalid');
                    this.setFieldError(field, 'Adresse email invalide.');
                    return false;
                }
                break;
                
            case 'date':
                if (value && field.hasAttribute('min')) {
                    const minDate = new Date(field.getAttribute('min'));
                    const selectedDate = new Date(value);
                    if (selectedDate < minDate) {
                        field.classList.add('is-invalid');
                        this.setFieldError(field, 'La date ne peut pas être antérieure à la date minimale.');
                        return false;
                    }
                }
                break;
                
            case 'number':
                if (value) {
                    const min = field.getAttribute('min');
                    const max = field.getAttribute('max');
                    const numValue = parseFloat(value);
                    
                    if (isNaN(numValue)) {
                        field.classList.add('is-invalid');
                        this.setFieldError(field, 'Veuillez saisir un nombre valide.');
                        return false;
                    }
                    
                    if (min && numValue < parseFloat(min)) {
                        field.classList.add('is-invalid');
                        this.setFieldError(field, `La valeur doit être supérieure ou égale à ${min}.`);
                        return false;
                    }
                    
                    if (max && numValue > parseFloat(max)) {
                        field.classList.add('is-invalid');
                        this.setFieldError(field, `La valeur doit être inférieure ou égale à ${max}.`);
                        return false;
                    }
                }
                break;
        }
        
        // Validation custom
        const pattern = field.getAttribute('pattern');
        if (pattern && value) {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                field.classList.add('is-invalid');
                this.setFieldError(field, 'Format invalide.');
                return false;
            }
        }
        
        // Champ valide
        if (value || !required) {
            field.classList.add('is-valid');
            this.clearFieldError(field);
        }
        
        return true;
    },
    
    // Validation d'un formulaire complet
    validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    // Définir un message d'erreur pour un champ
    setFieldError(field, message) {
        this.clearFieldError(field);
        
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        feedback.setAttribute('data-field-error', 'true');
        
        field.parentNode.appendChild(feedback);
    },
    
    // Supprimer le message d'erreur d'un champ
    clearFieldError(field) {
        const existingFeedback = field.parentNode.querySelector('[data-field-error]');
        if (existingFeedback) {
            existingFeedback.remove();
        }
    },
    
    // Validation email
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // Recherche en temps réel
    performLiveSearch(input) {
        const searchTerm = input.value.toLowerCase();
        const targetSelector = input.getAttribute('data-search-target') || 'tbody tr';
        const targets = document.querySelectorAll(targetSelector);
        
        targets.forEach(target => {
            const text = target.textContent.toLowerCase();
            const shouldShow = text.includes(searchTerm);
            
            target.style.display = shouldShow ? '' : 'none';
            
            // Animation
            if (shouldShow) {
                target.style.opacity = '0';
                setTimeout(() => {
                    target.style.transition = 'opacity 200ms ease';
                    target.style.opacity = '1';
                }, 10);
            }
        });
        
        // Mettre à jour le compteur de résultats
        const visibleCount = Array.from(targets).filter(t => t.style.display !== 'none').length;
        this.updateSearchResultsCount(visibleCount, targets.length);
    },
    
    // Mettre à jour le compteur de résultats de recherche
    updateSearchResultsCount(visible, total) {
        let counter = document.getElementById('searchResultsCount');
        if (!counter) {
            counter = document.createElement('div');
            counter.id = 'searchResultsCount';
            counter.className = 'text-muted small mt-1';
            
            const searchInput = document.querySelector('[data-live-search]');
            if (searchInput) {
                searchInput.parentNode.appendChild(counter);
            }
        }
        
        if (visible === total) {
            counter.textContent = '';
        } else {
            counter.textContent = `${visible} résultat(s) sur ${total}`;
        }
    },
    
    // Basculer entre les vues
    toggleView(viewType) {
        const views = document.querySelectorAll('[data-view]');
        const buttons = document.querySelectorAll('[data-view-toggle]');
        
        views.forEach(view => {
            const isTarget = view.getAttribute('data-view') === viewType;
            view.style.display = isTarget ? 'block' : 'none';
            
            if (isTarget) {
                view.style.opacity = '0';
                setTimeout(() => {
                    view.style.transition = 'opacity 300ms ease';
                    view.style.opacity = '1';
                }, 10);
            }
        });
        
        buttons.forEach(button => {
            const isActive = button.getAttribute('data-view-toggle') === viewType;
            button.classList.toggle('btn-light', isActive);
            button.classList.toggle('btn-outline-light', !isActive);
        });
        
        // Sauvegarder la préférence
        localStorage.setItem('preferred_view', viewType);
    },
    
    // Restaurer la vue préférée
    restorePreferredView() {
        const preferredView = localStorage.getItem('preferred_view');
        if (preferredView) {
            const button = document.querySelector(`[data-view-toggle="${preferredView}"]`);
            if (button) {
                button.click();
            }
        }
    },
    
    // Sauvegarde automatique des formulaires
    setupAutoSave(form) {
        const formId = form.id || 'form_' + Math.random().toString(36).substr(2, 9);
        const fields = form.querySelectorAll('input, select, textarea');
        
        // Charger les données sauvegardées
        fields.forEach(field => {
            const savedValue = localStorage.getItem(`${formId}_${field.name}`);
            if (savedValue && !field.value) {
                field.value = savedValue;
            }
        });
        
        // Sauvegarder les modifications
        fields.forEach(field => {
            field.addEventListener('input', () => {
                localStorage.setItem(`${formId}_${field.name}`, field.value);
            });
        });
        
        // Nettoyer après soumission réussie
        form.addEventListener('submit', () => {
            if (this.validateForm(form)) {
                fields.forEach(field => {
                    localStorage.removeItem(`${formId}_${field.name}`);
                });
            }
        });
    },
    
    // Masquage automatique des alertes
    autoHideAlerts() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert.parentNode && !alert.classList.contains('show')) {
                    alert.style.transition = 'opacity 500ms ease';
                    alert.style.opacity = '0';
                    
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, this.config.autoHideAlertDelay);
        });
    },
    
    // Initialisation des tableaux de données
    initializeDataTables() {
        const tables = document.querySelectorAll('[data-table="sortable"]');
        
        tables.forEach(table => {
            this.makeSortable(table);
        });
    },
    
    // Rendre un tableau triable
    makeSortable(table) {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(table, header);
            });
            
            // Ajouter l'icône de tri
            const icon = document.createElement('i');
            icon.className = 'bi bi-arrow-down-up ms-2';
            header.appendChild(icon);
        });
    },
    
    // Trier un tableau
    sortTable(table, header) {
        const index = Array.from(header.parentNode.children).indexOf(header);
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const sortType = header.getAttribute('data-sort');
        const isAsc = header.classList.contains('sort-asc');
        
        // Supprimer les classes de tri de tous les headers
        table.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        
        // Ajouter la classe de tri au header actuel
        header.classList.add(isAsc ? 'sort-desc' : 'sort-asc');
        
        // Trier les lignes
        rows.sort((a, b) => {
            const aValue = this.getCellValue(a, index, sortType);
            const bValue = this.getCellValue(b, index, sortType);
            
            let comparison = 0;
            if (aValue > bValue) comparison = 1;
            if (aValue < bValue) comparison = -1;
            
            return isAsc ? -comparison : comparison;
        });
        
        // Réorganiser les lignes
        rows.forEach(row => tbody.appendChild(row));
    },
    
    // Obtenir la valeur d'une cellule pour le tri
    getCellValue(row, index, sortType) {
        const cell = row.children[index];
        let value = cell.textContent.trim();
        
        switch (sortType) {
            case 'number':
                return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
            case 'date':
                return new Date(value).getTime() || 0;
            default:
                return value.toLowerCase();
        }
    },
    
    // Configuration des barres de progression
    setupProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar[data-animate]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progressBar = entry.target;
                    const targetWidth = progressBar.getAttribute('aria-valuenow');
                    
                    progressBar.style.width = '0%';
                    setTimeout(() => {
                        progressBar.style.transition = 'width 1s ease-in-out';
                        progressBar.style.width = targetWidth + '%';
                    }, 100);
                    
                    observer.unobserve(progressBar);
                }
            });
        });
        
        progressBars.forEach(bar => observer.observe(bar));
    },
    
    // Utilitaires
    utils: {
        // Formater un nombre avec des espaces
        formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        },
        
        // Formater une date
        formatDate(date) {
            return new Intl.DateTimeFormat('fr-FR').format(new Date(date));
        },
        
        // Debounce function
        debounce(func, delay) {
            let timeoutId;
            return function (...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        },
        
        // Throttle function
        throttle(func, delay) {
            let inThrottle;
            return function (...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, delay);
                }
            };
        }
    }
};

// Fonctions globales pour la compatibilité
window.validateForm = (form) => App.validateForm(form);
window.isValidEmail = (email) => App.isValidEmail(email);
window.exportData = () => {
    console.log('Export function called - implement specific export logic');
};

// Initialisation au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    App.init();
    App.autoHideAlerts();
    App.restorePreferredView();
});

// Gestion des erreurs globales
window.addEventListener('error', (e) => {
    console.error('Erreur JavaScript:', e.error);
});

// Export pour les modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}