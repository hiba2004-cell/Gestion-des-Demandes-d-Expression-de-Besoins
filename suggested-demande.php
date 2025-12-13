<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/database.php';


$pdo = getConnection();

// Fetch all available materials with type information
$stmt = $pdo->query("
    SELECT am.*, tb.libelle as type_libelle 
    FROM available_material am 
    JOIN types_besoins tb ON am.type_besoin_id = tb.id 
    ORDER BY am.date_ajout DESC
");
$materials = $stmt->fetchAll();

unset($material);

// Fetch types for filter
$typesStmt = $pdo->query("SELECT * FROM types_besoins");
$types = $typesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue du Matériel Disponible | Expression des Besoins</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="assets/css/demande-style.css" rel="stylesheet">

</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="header-title animate__animated animate__fadeInDown">
                            <i class="bi bi-box-seam me-2"></i>Catalogue du Matériel
                        </h1>
                        <p class="header-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                            Découvrez et demandez le matériel disponible pour votre service
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <div class="user-badge animate__animated animate__fadeIn animate__delay-1s">
                            <i class="bi bi-person-circle"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-4">
        <!-- Stats Section -->
        <section class="stats-section mb-4">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card animate__animated animate__fadeInUp">
                        <div class="stat-icon primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count($materials); ?></h3>
                            <p>Articles disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="stat-icon success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count(array_filter($materials, fn($m) => $m['statut'] === 'Disponible')); ?>
                            </h3>
                            <p>En stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="stat-icon warning">
                            <i class="bi bi-bookmark-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count(array_filter($materials, fn($m) => $m['statut'] === 'Réservé')); ?>
                            </h3>
                            <p>Réservés</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <div class="stat-icon info">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo count($types); ?></h3>
                            <p>Catégories</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="filter-section animate__animated animate__fadeIn">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="filter-title">
                        <i class="bi bi-funnel"></i>
                        Filtrer par catégorie
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="filter-btn active" data-filter="all">
                            <i class="bi bi-grid-3x3-gap me-1"></i> Tous
                        </button>
                        <?php foreach ($types as $type): ?>
                        <button class="filter-btn" data-filter="<?php echo $type['id']; ?>">
                            <?php echo htmlspecialchars($type['libelle']); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <input type="text" id="searchInput" class="form-control search-input" style="padding-left:3rem"
                        placeholder="Rechercher un article...">
                </div>
            </div>
        </section>

        <!-- Materials Grid -->
        <section class="materials-section">
            <div class="materials-grid" id="materialsGrid">
                <?php if (empty($materials)): ?>
                <div class="empty-state col-12">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3>Aucun matériel disponible</h3>
                    <p>Il n'y a actuellement aucun matériel disponible dans le catalogue.</p>
                </div>
                <?php else: ?>
                <?php foreach ($materials as $index => $material): ?>
                <div class="material-card animate__animated animate__fadeInUp"
                    data-type="<?php echo $material['type_besoin_id']; ?>"
                    data-title="<?php echo strtolower(htmlspecialchars($material['titre'])); ?>"
                    style="animation-delay: <?php echo $index * 0.05; ?>s">
                    <div class="material-image">
                        <img src="<?php echo $material['image_url'] ?: 'https://via.placeholder.com/400x200/e2e8f0/64748b?text=Image'; ?>"
                            alt="<?php echo htmlspecialchars($material['titre']); ?>" loading="lazy">
                        <span class="material-type-badge">
                            <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($material['type_libelle']); ?>
                        </span>

                        <span class="material-badge <?php 
                                echo $material['statut'] === 'Disponible' ? 'disponible' : 
                                    ($material['statut'] === 'Réservé' ? 'reserve' : 'indisponible'); 
                            ?>">
                            <?php echo $material['statut']; ?>
                        </span>
                    </div>
                    <div class="material-content">
                        <h3 class="material-title"><?php echo htmlspecialchars($material['titre']); ?></h3>
                        <p class="material-description"><?php echo htmlspecialchars($material['description']); ?></p>
                        <div class="material-meta">
                            <div class="quantity-badge">
                                <i class="bi bi-boxes"></i>
                                <span><?php echo $material['quantite_disponible']; ?> disponible(s)</span>
                            </div>
                            <button class="btn-demand" data-id="<?php echo $material['id']; ?>"
                                data-title="<?php echo htmlspecialchars($material['titre']); ?>"
                                data-description="<?php echo htmlspecialchars($material['description']); ?>"
                                data-type="<?php echo $material['type_besoin_id']; ?>"
                                data-image="<?php echo $material['image_url']; ?>"
                                data-type-label="<?php echo htmlspecialchars($material['type_libelle']); ?>"
                                <?php echo $material['statut'] !== 'Disponible' ? 'disabled' : ''; ?>
                                <?php echo $material['statut'] === 'Disponible' ? 'data-bs-toggle="modal" data-bs-target="#demandModal"' : ''; ?>>
                                <i class="bi bi-send"></i>
                                Demander
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Demand Modal -->
    <div class="modal fade" id="demandModal" tabindex="-1" aria-labelledby="demandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demandModalLabel">
                        <i class="bi bi-file-earmark-plus me-2"></i>Nouvelle Demande de Matériel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form id="demandForm">
                    <div class="modal-body">
                        <!-- Material Preview -->
                        <div class="material-preview" id="materialPreview">
                            <div class="material-preview-image">
                                <img id="previewImage" src="/placeholder.svg" alt="Material Preview">
                            </div>
                            <div class="material-preview-info">
                                <h4 id="previewTitle"></h4>
                                <p id="previewDescription"></p>
                                <span class="material-preview-badge" id="previewType"></span>
                                <span class="material-preview-price" id="previewPrice"></span>
                            </div>
                        </div>

                        <input type="hidden" id="materialId" name="material_id">
                        <input type="hidden" id="typeId" name="type_besoin_id">

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-chat-left-text me-1"></i>
                                Description de votre besoin
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                placeholder="Décrivez pourquoi vous avez besoin de ce matériel et comment vous comptez l'utiliser..."
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Niveau d'urgence
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="urgency-option w-100">
                                        <input type="radio" name="urgence" value="Faible" class="d-none" checked>
                                        <div class="urgency-icon low">
                                            <i class="bi bi-arrow-down"></i>
                                        </div>
                                        <div class="urgency-content">
                                            <strong>Faible</strong>
                                            <small class="d-block text-muted">Pas pressé</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="urgency-option w-100">
                                        <input type="radio" name="urgence" value="Moyenne" class="d-none">
                                        <div class="urgency-icon medium">
                                            <i class="bi bi-dash-lg"></i>
                                        </div>
                                        <div class="urgency-content">
                                            <strong>Moyenne</strong>
                                            <small class="d-block text-muted">Dans la semaine</small>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="urgency-option w-100">
                                        <input type="radio" name="urgence" value="Urgente" class="d-none">
                                        <div class="urgency-icon high">
                                            <i class="bi bi-arrow-up"></i>
                                        </div>
                                        <div class="urgency-content">
                                            <strong>Urgente</strong>
                                            <small class="d-block text-muted">Immédiat</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <span class="btn-text">
                                <i class="bi bi-send me-1"></i>Envoyer la demande
                            </span>
                            <span class="loading-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function() {
        // Filter functionality
        $('.filter-btn').on('click', function() {
            const filter = $(this).data('filter');

            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            if (filter === 'all') {
                $('.material-card').fadeIn(300);
            } else {
                $('.material-card').each(function() {
                    if ($(this).data('type') == filter) {
                        $(this).fadeIn(300);
                    } else {
                        $(this).fadeOut(300);
                    }
                });
            }
        });

        // Search functionality
        $('#searchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();

            $('.material-card').each(function() {
                const title = $(this).data('title');
                if (title.includes(searchTerm)) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        });

        // Open demand modal and populate data
        $('.btn-demand').on('click', function() {
            if ($(this).is(':disabled')) return;

            const id = $(this).data('id');
            const title = $(this).data('title');
            const description = $(this).data('description');
            const typeId = $(this).data('type');
            const typeLabel = $(this).data('type-label');
            const image = $(this).data('image') ||
                'https://via.placeholder.com/120x120/e2e8f0/64748b?text=Image';
            const price = $(this).data('price');

            $('#materialId').val(id);
            $('#typeId').val(typeId);
            $('#previewTitle').text(title);
            $('#previewDescription').text(description.substring(0, 100) + '...');
            $('#previewType').text(typeLabel);
            $('#previewImage').attr('src', image);
            // Pre-fill description
            $('#description').val('Demande pour: ' + title + '\n\n');
        });

        // Form submission
        $('#demandForm').on('submit', function(e) {
            e.preventDefault();

            const $submitBtn = $('#submitBtn');
            const $btnText = $submitBtn.find('.btn-text');
            const $spinner = $submitBtn.find('.loading-spinner');

            // Show loading state
            $btnText.hide();
            $spinner.css('display', 'inline-block');
            $submitBtn.prop('disabled', true);

            // Collect form data
            const formData = {
                material_id: $('#materialId').val(),
                type_besoin_id: $('#typeId').val(),
                description: $('#description').val(),
                urgence: $('input[name="urgence"]:checked').val()
            };

            // AJAX request
            $.ajax({
                url: 'ajax/submit_demand.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Close modal
                    $('#demandModal').modal('hide');

                    // Show success toast
                    showToast('success', 'Demande envoyée!',
                        'Votre demande a été soumise avec succès.');

                    // Reset form
                    $('#demandForm')[0].reset();
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur',
                        'Une erreur est survenue. Veuillez réessayer. ');
                },
                complete: function() {
                    // Reset button state
                    $btnText.show();
                    $spinner.hide();
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        // Toast notification function
        function showToast(type, title, message) {
            const iconClass = type === 'success' ? 'bi-check-lg' : 'bi-x-lg';
            const borderColor = type === 'success' ? 'var(--success)' : 'var(--danger)';
            const bgColor = type === 'success' ?
                'linear-gradient(135deg, #10b981 0%, #059669 100%)' :
                'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';

            const toast = $(`
                <div class="custom-toast animate__animated animate__fadeInUp" style="border-left-color: ${borderColor}">
                    <div class="toast-icon" style="background: ${bgColor}">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="toast-content">
                        <h5>${title}</h5>
                        <p>${message}</p>
                    </div>
                </div>
            `);

            $('#toastContainer').append(toast);

            // Auto remove after 5 seconds
            setTimeout(function() {
                toast.addClass('animate__fadeOutDown');
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 5000);
        }

        // Reset modal on close
        $('#demandModal').on('hidden.bs.modal', function() {
            $('#demandForm')[0].reset();
        });
    });
    </script>
</body>

</html>