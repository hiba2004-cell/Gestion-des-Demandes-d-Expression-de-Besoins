        </div> <!-- Fin du container-fluid -->
    </div> <!-- Fin du main-content -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé pour le dashboard -->
    <script src="assets/js/script-dashboard.js"></script>
    
    <script>
        // Configuration globale des animations et interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation des tooltips
            initializeTooltips();
            
            // Animations au scroll
            setupScrollAnimations();
            
            // Gestion des modals
            setupModals();
            
            // Raccourcis clavier
            setupKeyboardShortcuts();
            
            // Mise à jour automatique du statut en ligne
            updateOnlineStatus();
            setInterval(updateOnlineStatus, 30000); // Toutes les 30 secondes
        });

        // Initialisation des tooltips Bootstrap
        function initializeTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    delay: { show: 500, hide: 100 },
                    animation: true
                });
            });
        }

        // Animations au scroll avec Intersection Observer
        function setupScrollAnimations() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        
                        // Animation spécifique pour les cartes
                        if (entry.target.classList.contains('modern-card')) {
                            entry.target.style.transform = 'translateY(0)';
                            entry.target.style.opacity = '1';
                        }
                        
                        // Animation pour les éléments de liste
                        if (entry.target.tagName === 'TR') {
                            entry.target.style.animation = 'slideInFromLeft 0.6s ease forwards';
                        }
                    }
                });
            }, observerOptions);

            // Observer les éléments animables
            document.querySelectorAll('.modern-card, .stats-card, tr').forEach(el => {
                observer.observe(el);
            });
        }

        // Configuration des modals
        function setupModals() {
            // Confirmation de suppression
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
                    e.preventDefault();
                    showConfirmModal(
                        'Êtes-vous sûr de vouloir supprimer cet élément ?',
                        'Cette action est irréversible.',
                        function() {
                            const href = e.target.closest('.btn-delete').getAttribute('href');
                            if (href) window.location.href = href;
                        }
                    );
                }
            });
        }

        // Modal de confirmation personnalisée
        function showConfirmModal(title, message, onConfirm) {
            const modalId = 'confirmModal_' + Date.now();
            const modalHTML = `
                <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                    ${title}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <p class="mb-0">${message}</p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i> Annuler
                                </button>
                                <button type="button" class="btn btn-danger" id="confirmBtn">
                                    <i class="bi bi-check me-1"></i> Confirmer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Ajouter la modal au DOM
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            
            // Gestion de la confirmation
            document.getElementById('confirmBtn').addEventListener('click', function() {
                modal.hide();
                if (onConfirm) onConfirm();
            });
            
            // Nettoyage après fermeture
            document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
            
            modal.show();
        }

        // Raccourcis clavier
        function setupKeyboardShortcuts() {
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + Shift + N : Nouvelle demande (pour les demandeurs)
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'N') {
                    e.preventDefault();
                    const newRequestBtn = document.querySelector('a[href*="nouvelle-demande"]');
                    if (newRequestBtn) newRequestBtn.click();
                }
                
                // Ctrl/Cmd + Shift + D : Dashboard
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                    e.preventDefault();
                    window.location.href = '<?php echo redirectByRole($user['role']); ?>';
                }
                
                // Ctrl/Cmd + Shift + L : Déconnexion
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'L') {
                    e.preventDefault();
                    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
                        window.location.href = 'login.php?logout=1';
                    }
                }
                
                // Échap : Fermer la sidebar sur mobile
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });
        }

        // Mise à jour du statut en ligne
        function updateOnlineStatus() {
            fetch('api/update-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    action: 'ping'
                })
            }).catch(error => {
                console.log('Erreur de connexion:', error);
            });
        }

        // Notifications en temps réel (WebSocket simulation)
        function startNotificationPolling() {
            let lastNotificationCheck = Date.now();
            
            setInterval(() => {
                fetch(`api/check-new-notifications.php?since=${lastNotificationCheck}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.hasNew) {
                            // Mettre à jour le badge de notifications
                            updateNotificationBadge(data.count);
                            
                            // Afficher une notification toast
                            if (data.latest) {
                                showNotificationToast(data.latest);
                            }
                            
                            // Recharger la liste des notifications
                            loadNotifications();
                        }
                        lastNotificationCheck = Date.now();
                    })
                    .catch(error => console.error('Erreur polling notifications:', error));
            }, 10000); // Vérifier toutes les 10 secondes
        }

        // Mettre à jour le badge de notifications
        function updateNotificationBadge(count) {
            const badges = document.querySelectorAll('.notification-badge');
            badges.forEach(badge => {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            });
        }

        // Afficher une notification toast
        function showNotificationToast(notification) {
            const toastId = 'toast_' + Date.now();
            const toastHTML = `
                <div class="toast" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true" 
                     style="position: fixed; top: 100px; right: 20px; z-index: 1060; min-width: 350px;">
                    <div class="toast-header">
                        <i class="bi bi-bell-fill text-primary me-2"></i>
                        <strong class="me-auto">Nouvelle notification</strong>
                        <small class="text-muted">À l'instant</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        <strong>${notification.titre}</strong><br>
                        ${notification.message}
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toastHTML);
            
            const toast = new bootstrap.Toast(document.getElementById(toastId), {
                delay: 5000,
                autohide: true
            });
            
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
            
            toast.show();
        }

        // Validation de formulaires améliorée
        function setupFormValidation() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(this)) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Animation de secousse
                        this.style.animation = 'shake 0.5s ease-in-out';
                        setTimeout(() => {
                            this.style.animation = '';
                        }, 500);
                    }
                });
                
                // Validation en temps réel
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        validateField(this);
                    });
                });
            });
        }

        // Validation d'un champ
        function validateField(field) {
            const value = field.value.trim();
            const required = field.hasAttribute('required');
            
            field.classList.remove('is-valid', 'is-invalid');
            
            if (required && !value) {
                field.classList.add('is-invalid');
                return false;
            }
            
            // Validation selon le type
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    field.classList.add('is-invalid');
                    return false;
                }
            }
            
            if (value || !required) {
                field.classList.add('is-valid');
            }
            
            return true;
        }

        // Effet de ripple sur les boutons
        function addRippleEffect() {
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn')) {
                    const button = e.target;
                    const rect = button.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        background-color: rgba(255,255,255,0.6);
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        pointer-events: none;
                    `;
                    
                    button.style.position = 'relative';
                    button.style.overflow = 'hidden';
                    button.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                }
            });
        }

        // Sauvegarde automatique des formulaires
        function setupAutoSave() {
            const forms = document.querySelectorAll('form[data-autosave]');
            
            forms.forEach(form => {
                const formId = form.id || 'autosave_' + Math.random().toString(36).substr(2, 9);
                const fields = form.querySelectorAll('input, select, textarea');
                
                // Charger les données sauvegardées
                fields.forEach(field => {
                    const savedValue = localStorage.getItem(`${formId}_${field.name}`);
                    if (savedValue && !field.value) {
                        field.value = savedValue;
                    }
                });
                
                // Sauvegarder à chaque modification
                fields.forEach(field => {
                    field.addEventListener('input', function() {
                        localStorage.setItem(`${formId}_${this.name}`, this.value);
                    });
                });
                
                // Nettoyer après soumission
                form.addEventListener('submit', function() {
                    fields.forEach(field => {
                        localStorage.removeItem(`${formId}_${field.name}`);
                    });
                });
            });
        }

        // Animations CSS personnalisées
        const customCSS = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
            
            @keyframes slideInFromLeft {
                from { transform: translateX(-20px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            .animate-in {
                animation: fadeInUp 0.6s ease forwards;
            }
            
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        
        const style = document.createElement('style');
        style.textContent = customCSS;
        document.head.appendChild(style);

        // Initialiser toutes les fonctionnalités
        setupFormValidation();
        addRippleEffect();
        setupAutoSave();
        startNotificationPolling();

        // Gestion des erreurs globales
        window.addEventListener('error', function(e) {
            console.error('Erreur JavaScript:', e.error);
        });

        // Performance : lazy loading des images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Message de maintenance (si nécessaire)
        function checkMaintenanceMode() {
            fetch('api/check-maintenance.php')
                .then(response => response.json())
                .then(data => {
                    if (data.maintenance) {
                        showMaintenanceAlert(data.message);
                    }
                })
                .catch(error => console.error('Erreur vérification maintenance:', error));
        }
        
        function showMaintenanceAlert(message) {
            const alertHTML = `
                <div class="alert alert-warning alert-dismissible fade show position-fixed" 
                     style="top: 20px; left: 50%; transform: translateX(-50%); z-index: 1070; min-width: 400px;" 
                     role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Maintenance programmée :</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.insertAdjacentHTML('afterbegin', alertHTML);
        }

        // Vérifier la maintenance au chargement
        setTimeout(checkMaintenanceMode, 2000);
    </script>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">
                        &copy; 2024 Système d'Expression du Besoin - 
                        <a href="pages/mentions-legales.php" class="text-decoration-none">Mentions légales</a>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        Version 2.0 | 
                        <a href="pages/aide.php" class="text-decoration-none">Aide</a> | 
                        <a href="pages/contact.php" class="text-decoration-none">Contact</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>