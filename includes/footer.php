                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript personnalisé -->
    <script src="assets/js/script.js"></script>
    
    <script>
        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des cartes
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Animation des boutons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Confirmation de suppression
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                        window.location.href = href;
                    }
                });
            });

            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    }
                }, 5000);
            });
        });

        // Fonction d'export des données
        function exportData() {
            const currentPage = window.location.pathname;
            let exportUrl = '';
            
            if (currentPage.includes('liste-besoins.php')) {
                exportUrl = 'export.php?type=besoins';
            } else if (currentPage.includes('statistiques.php')) {
                exportUrl = 'export.php?type=statistics';
            } else {
                exportUrl = 'export.php?type=all';
            }
            
            // Créer un lien temporaire pour le téléchargement
            const link = document.createElement('a');
            link.href = exportUrl;
            link.download = 'export_besoins_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Validation des formulaires côté client
        function validateForm(form) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });
            
            // Validation email
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });
            
            // Validation date
            const dateFields = form.querySelectorAll('input[type="date"]');
            dateFields.forEach(field => {
                if (field.value) {
                    const selectedDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        field.classList.add('is-invalid');
                        // Afficher un message d'erreur personnalisé
                        let feedback = field.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            field.parentNode.insertBefore(feedback, field.nextSibling);
                        }
                        feedback.textContent = 'La date ne peut pas être antérieure à aujourd\'hui';
                        isValid = false;
                    }
                }
            });
            
            return isValid;
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Recherche en temps réel
        function setupLiveSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.toLowerCase();
                        const rows = document.querySelectorAll('tbody tr');
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    }, 300);
                });
            }
        }

        // Initialiser la recherche live si présente
        document.addEventListener('DOMContentLoaded', setupLiveSearch);

        // Tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Popover Bootstrap
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        const collapseEl = document.getElementById('mainNavbar');
        const myToggleNavBar = document.getElementById('my-toggler');
        myToggleNavBar.addEventListener("click", function () {
            // console.log(collapseEl.style.display);
            if (collapseEl.style.display === "none" || collapseEl.style.display == "") {
                collapseEl.style.display = "block";
            } else {
                collapseEl.style.display = "none";
            }
        });
    </script>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3">
        <div class="container">
            <p class="mb-0">
                &copy; 2024 Système d'Expression du Besoin - 
                <a href="#" class="text-decoration-none">Documentation</a> | 
                <a href="#" class="text-decoration-none">Support</a>
            </p>
        </div>
    </footer>
</body>
</html>