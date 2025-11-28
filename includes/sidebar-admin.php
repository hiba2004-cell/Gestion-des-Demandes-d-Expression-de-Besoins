 <div class="row">
     <!-- Sidebar -->
            <nav id="mainNavbar" class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                                href="/besoins/dashboard-admin.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'liste-besoins.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/liste-besoins.php">
                                <i class="bi bi-list-ul me-2"></i>
                                Liste des Besoins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'liste-utilisateurs.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/liste-utilisateurs.php">
                                <i class="bi bi-people me-2"></i>
                                Liste Utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'ajouter-besoin.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/ajouter-besoin.php">
                                <i class="bi bi-plus-circle me-2"></i>
                                Ajouter un Besoin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'statistiques.php') ? 'active' : ''; ?>"
                                href="/besoins/pages/statistiques.php">
                                <i class="bi bi-bar-chart me-2"></i>
                                Statistiques
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <h6 class="text-white sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-uppercase">
                                <span>Filtres Rapides</span>
                            </h6>

                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=nouveau">
                                <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem;"></i>
                                Nouveaux Besoins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?priorite=critique">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"
                                    style="font-size: 0.8rem;"></i>
                                Priorité Critique
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/besoins/pages/liste-besoins.php?statut=en_cours">
                                <i class="bi bi-clock-fill text-warning me-2" style="font-size: 0.8rem;"></i>
                                En Cours
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>