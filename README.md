# 📋 Système d'Expression du Besoin

Application web complète pour recueillir, analyser et gérer les expressions de besoins des utilisateurs/clients avec une interface moderne Bootstrap 5.

## 🚀 Fonctionnalités

### 📊 Dashboard Principal
- Vue d'ensemble avec statistiques en temps réel
- Cartes interactives avec animations
- Graphiques dynamiques (Chart.js)
- Besoins récents et alertes

### ➕ Gestion des Besoins
- **Ajout de besoin** : Formulaire complet avec validation
- **Liste des besoins** : Tableau avec filtres et pagination
- **Détail du besoin** : Vue complète avec possibilité de modification
- **Recherche avancée** : Par priorité, statut, catégorie, demandeur

### 📈 Statistiques et Reporting
- Graphiques de répartition par statut et priorité
- Évolution mensuelle des besoins
- Analyse des délais et performances
- Export des données en CSV
- Insights et recommandations automatiques

### 🎨 Interface Utilisateur
- Design responsive (mobile/desktop)
- Thème moderne avec Bootstrap 5.3
- Navigation intuitive avec sidebar
- Animations et transitions fluides
- Mode d'affichage tableau/cartes

## 🛠️ Technologies Utilisées

### Backend
- **PHP 7.4+** avec PDO pour la base de données
- **MySQL 8.0** pour le stockage des données
- Architecture MVC simple et claire
- Validation des données côté serveur
- Protection contre XSS, CSRF, injection SQL

### Frontend
- **HTML5** sémantique
- **CSS3** avec variables personnalisées
- **Bootstrap 5.3** pour la responsivité
- **JavaScript ES6+** pour les interactions
- **Chart.js** pour les graphiques
- **Bootstrap Icons** pour les icônes

### Sécurité
- Validation des entrées (client + serveur)
- Protection CSRF avec tokens
- Échappement des données (XSS)
- Requêtes préparées (injection SQL)
- Sessions PHP sécurisées

## ⚙️ Installation

### Prérequis
- PHP 7.4+ avec extensions :
  - PDO_MySQL
  - mbstring
  - session
- MySQL 8.0+
- Serveur web (Apache/Nginx)
- Navigateur moderne

### Configuration de la Base de Données

1. **Créer la base de données** :
```sql
mysql -u root -p
```

2. **Importer le schéma** :
```sql
source sql/database.sql
```

3. **Configurer la connexion** dans `config/database.php` :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'expression_besoin');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### Déploiement

1. **Cloner/Télécharger** les fichiers dans votre répertoire web
2. **Configurer** la base de données (voir ci-dessus)
3. **Ajuster les permissions** si nécessaire :
```bash
chmod 755 -R .
chmod 644 *.php
```

4. **Accéder** à l'application via votre navigateur

## 📂 Structure du Projet

```
expression-besoin/
├── 📁 config/
│   └── database.php          # Configuration BDD
├── 📁 includes/
│   ├── header.php           # En-tête HTML avec navigation
│   ├── footer.php           # Pied de page avec scripts
│   └── functions.php        # Fonctions utilitaires
├── 📁 pages/
│   ├── ajouter-besoin.php   # Formulaire d'ajout
│   ├── liste-besoins.php    # Liste avec filtres
│   ├── detail-besoin.php    # Détail et modification
│   └── statistiques.php     # Tableau de bord stats
├── 📁 assets/
│   ├── 📁 css/
│   │   └── style.css        # Styles personnalisés
│   └── 📁 js/
│       └── script.js        # Scripts JavaScript
├── 📁 sql/
│   └── database.sql         # Schéma de base de données
├── index.php                # Page d'accueil/dashboard
└── README.md               # Documentation
```

## 💾 Schéma de Base de Données

### Table `besoins`
| Champ | Type | Description |
|-------|------|-------------|
| `id` | INT AUTO_INCREMENT | Identifiant unique |
| `titre` | VARCHAR(200) | Titre du besoin |
| `description` | TEXT | Description détaillée |
| `priorite` | ENUM | faible, moyenne, haute, critique |
| `statut` | ENUM | nouveau, en_cours, termine, rejete |
| `categorie` | VARCHAR(100) | Catégorie du besoin |
| `demandeur_nom` | VARCHAR(100) | Nom du demandeur |
| `demandeur_email` | VARCHAR(150) | Email du demandeur |
| `cout_estime` | DECIMAL(10,2) | Coût estimé en euros |
| `delai_souhaite` | DATE | Date limite souhaitée |
| `date_creation` | TIMESTAMP | Date de création |
| `date_modification` | TIMESTAMP | Dernière modification |

### Table `categories` (optionnelle)
- Gestion des catégories prédéfinies
- Extension future pour une meilleure organisation

## 🎯 Fonctionnalités Détaillées

### 1. Dashboard (index.php)
- **KPI principaux** : Total besoins, terminés, en retard, budget
- **Graphiques interactifs** : Chart.js avec animations
- **Besoins récents** : Tableau des 5 derniers besoins
- **Alertes** : Besoins critiques et notifications système

### 2. Ajout de Besoin (pages/ajouter-besoin.php)
- **Formulaire structuré** : Sections organisées logiquement
- **Validation temps réel** : JavaScript + PHP
- **Auto-sauvegarde** : localStorage pour éviter les pertes
- **Suggestions** : Catégories prédéfinies avec datalist

### 3. Liste des Besoins (pages/liste-besoins.php)
- **Filtres avancés** : Priorité, statut, catégorie, recherche
- **Double vue** : Tableau et cartes avec sauvegarde préférence
- **Pagination** : Performance optimisée
- **Actions en masse** : Export, suppression

### 4. Détail du Besoin (pages/detail-besoin.php)
- **Vue complète** : Toutes les informations organisées
- **Mode édition** : Modification in-situ
- **Actions** : Contact demandeur, impression, suppression
- **Historique** : Suivi des modifications

### 5. Statistiques (pages/statistiques.php)
- **Graphiques avancés** : Évolution, répartitions, tendances
- **Insights automatiques** : Analyses et recommandations
- **Export complet** : CSV avec toutes les métriques
- **Performance** : Délais, taux de complétion

## 🔧 Personnalisation

### Thème et Couleurs
Modifier les variables CSS dans `assets/css/style.css` :
```css
:root {
    --primary-color: #0056b3;
    --secondary-color: #6c757d;
    --success-color: #198754;
    /* ... */
}
```

### Catégories
Ajouter des catégories dans la table `categories` ou modifier directement dans les formulaires.

### Champs Supplémentaires
1. Modifier le schéma de base (`sql/database.sql`)
2. Adapter les fonctions (`includes/functions.php`)
3. Mettre à jour les formulaires

### Validation Personnalisée
Étendre les fonctions de validation dans `assets/js/script.js` et `includes/functions.php`.

## 🚀 Démarrage Rapide

1. **Télécharger** tous les fichiers dans votre répertoire web
2. **Configurer** la base de données (voir section Installation)
3. **Ouvrir** index.php dans votre navigateur
4. **Ajouter** votre premier besoin via le formulaire
5. **Explorer** toutes les fonctionnalités !

## 📞 Support et Maintenance

### Logs et Debugging
- **Erreurs PHP** : Vérifier les logs serveur
- **Erreurs JavaScript** : Console navigateur
- **Base de données** : Logs MySQL

### Problèmes Courants

#### Erreur de connexion BDD
```bash
# Vérifier la configuration
grep -n "DB_" config/database.php

# Tester la connexion MySQL
mysql -u username -p -e "SELECT 1;"
```

#### Problèmes d'affichage
```bash
# Vider le cache navigateur
# Vérifier les chemins CSS/JS
# Contrôler la console pour erreurs
```

## 📄 Licence

Projet open-source sous licence MIT.
Libre d'utilisation, modification et distribution.

**Bon développement ! 🎉**