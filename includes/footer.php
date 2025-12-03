</div>
</main>
</div>
</div>

<!-- Chat Toggle Button -->
<button id="chatToggleBtn" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    <i class="fas fa-comments"></i>
    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle" id="chatBadge" style="display: none;">1</span>
</button>

<!-- Chat Container -->
<div id="chatContainer" class="position-fixed z-10" style="bottom: 90px; right: 20px; display: none;">
    <div class="card" style="width: 350px; height: 500px;">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Assistant Virtual</h6>
            <button id="closeChatBtn" class="btn btn-sm btn-link text-white p-0">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body p-0" style="height: calc(100% - 56px);">
            <iframe
                id="dialogflowIframe"
                allow="microphone;"
                width="100%"
                height="100%"
                style="border: none;"
                src="https://console.dialogflow.com/api-client/demo/embedded/69e18598-4f7d-4d50-b21d-177c9adcfe12">
            </iframe>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript personnalisé -->
<script src="assets/js/script.js"></script>

<script>

// Chat toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const chatContainer = document.getElementById('chatContainer');
    const closeChatBtn = document.getElementById('closeChatBtn');
    const chatBadge = document.getElementById('chatBadge');
    const dialogflowIframe = document.getElementById('dialogflowIframe');
    
    let isChatOpen = false;
    
    // Toggle chat
    chatToggleBtn.addEventListener('click', function() {
        isChatOpen = !isChatOpen;
        
        if (isChatOpen) {
            chatContainer.style.display = 'block';
            chatToggleBtn.innerHTML = '<i class="fas fa-times"></i>';
            chatBadge.style.display = 'none';
            
        } else {
            chatContainer.style.display = 'none';
            chatToggleBtn.innerHTML = '<i class="fas fa-comments"></i>';
        }
    });
    
    // Close chat
    closeChatBtn.addEventListener('click', function() {
        chatContainer.style.display = 'none';
        chatToggleBtn.innerHTML = '<i class="fas fa-comments"></i>';
        isChatOpen = false;
    });
    
    
    
    // Listen for messages from Dialogflow iframe
    window.addEventListener('message', function(event) {
        // Handle messages from Dialogflow if needed
        if (event.data && event.data.type === 'DIALOGFLOW_RESPONSE') {
            console.log('Dialogflow response:', event.data);
            
            // Show notification badge when chat is closed
            if (!isChatOpen) {
                chatBadge.style.display = 'block';
            }
        }
    });
    
    // Show notification badge after some time (simulated)
    setTimeout(() => {
        if (!isChatOpen) {
            chatBadge.style.display = 'block';
        }
    }, 5000);
});

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
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Popover Bootstrap
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
});

const collapseEl = document.getElementById('mainNavbar');
const myToggleNavBar = document.getElementById('my-toggler');
myToggleNavBar.addEventListener("click", function() {
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
                            &copy; 2025/2026 Système d'Expression du Besoin -

                        </p>
                    </div>
                </footer>
                </body>

                </html>