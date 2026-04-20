# Structure du Projet Jolly Beauty

## Organisation des fichiers

```
Jolly_Beauty/
  admin/                     # Administration du site
    add-product.php
    index.php
  
  api/                       # APIs backend
    cart-sync.php           # Synchronisation panier
    check_promo.php         # Validation codes promo
    newsletter.php          # Newsletter
    stripe-create-session.php # Création sessions Stripe
    stripe-test.php         # Tests Stripe
  
  assets/                    # Ressources statiques
    css/                    # Styles
      style.css
      category-rich.css
      static-pages.css
    js/                     # JavaScript
      script.js
      category-rich.js
    images/                 # Images
      bijoux/
      brand/
      coffrets/
      slider/
    videos/                 # Vidéos
      bijou-creation.mp4
      coffret-presentation.mp4
      fabrication-artisanale.mp4
  
  docs/                      # Documentation
    README.md               # Documentation générale
    PROJECT_STRUCTURE.md    # Structure du projet
    STRIPE_INTEGRATION.md   # Guide Stripe
  
  includes/                  # Fichiers inclus
    config.php              # Configuration principale
    footer.php               # Pied de page
    header.php               # En-tête
    stripe-config.php        # Configuration Stripe
    partials/                # Partials
      category-extra-bijoux.php
      category-extra-coffrets.php
      category-extra-soins.php
  
  pages/                     # Pages principales
    index.php               # Accueil
    category.php            # Catégories
    product.php             # Fiche produit
    checkout.php            # Panier/paiement
    contact.php             # Contact
    login.php               # Connexion
    notre-histoire.php      # À propos
    media-gallery.php       # Galerie média
  
  payments/                  # Pages de paiement
    payment-success.php      # Succès paiement
    payment-cancelled.php    # Annulation paiement
  
  bijoux.php                 # Redirection catégorie bijoux
  coffrets.php               # Redirection catégorie coffrets
  soins-rituels.php          # Redirection catégorie soins
  rituels.php                # Redirection catégorie rituels
  
  category.php               # Redirection vers pages/category.php
  product.php                # Redirection vers pages/product.php
  checkout.php               # Redirection vers pages/checkout.php
  contact.php                # Redirection vers pages/contact.php
  login.php                  # Redirection vers pages/login.php
  notre-histoire.php         # Redirection vers pages/notre-histoire.php
  media-gallery.php          # Redirection vers pages/media-gallery.php
  payment-success.php        # Redirection vers payments/payment-success.php
  payment-cancelled.php      # Redirection vers payments/payment-cancelled.php
  
  index.php                  # Redirection vers pages/index.php
  
  database.sql               # Base de données
  update-stripe-db.php       # Mise à jour BDD pour Stripe
  test-integration.php       # Tests d'intégration
```

## Pages de redirection

Les fichiers à la racine sont des fichiers de redirection qui maintiennent la compatibilité avec les URLs existantes :

- `index.php` -> `pages/index.php`
- `category.php` -> `pages/category.php`
- `product.php` -> `pages/product.php`
- `checkout.php` -> `pages/checkout.php`
- `contact.php` -> `pages/contact.php`
- `login.php` -> `pages/login.php`
- `notre-histoire.php` -> `pages/notre-histoire.php`
- `media-gallery.php` -> `pages/media-gallery.php`
- `payment-success.php` -> `payments/payment-success.php`
- `payment-cancelled.php` -> `payments/payment-cancelled.php`

## Catégories

Les fichiers de catégories utilisent une variable `$categorySlug` pour charger la bonne catégorie :

- `bijoux.php` : `$categorySlug = 'bijoux'`
- `coffrets.php` : `$categorySlug = 'coffrets'`
- `soins-rituels.php` : `$categorySlug = 'soins'`
- `rituels.php` : `$categorySlug = 'produits'`

## Sécurité

- Les clés API Stripe sont dans `includes/stripe-config.php`
- Les fichiers de configuration ne sont pas accessibles via HTTP
- Validation des entrées utilisateur
- Protection contre les injections SQL

## Maintenance

- Utiliser `test-integration.php` pour vérifier l'installation
- Consulter les logs d'erreurs PHP pour le débogage
- Mettre à jour la documentation lors des modifications
