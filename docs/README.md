# Jolly Beauty - E-commerce Site

## Structure du projet

```
Jolly_Beauty/
  admin/                 # Administration
  api/                   # APIs backend
  assets/                # Ressources statiques
  docs/                  # Documentation
  includes/              # Fichiers inclus
  pages/                 # Pages principales
  payments/              # Pages de paiement
  config.php             # Configuration principale
  database.sql           # Base de données
```

## Pages principales

- `index.php` - Accueil
- `category.php` - Catégories de produits
- `product.php` - Fiche produit
- `checkout.php` - Panier et paiement
- `contact.php` - Contact
- `login.php` - Connexion/inscription
- `notre-histoire.php` - À propos

## Catégories

- `bijoux.php` -> `category.php?c=bijoux`
- `coffrets.php` -> `category.php?c=coffrets`
- `soins-rituels.php` -> `category.php?c=soins`
- `rituels.php` -> `category.php?c=produits`

## Paiement

Intégration Stripe Checkout avec :
- Carte bancaire
- Apple Pay
- Google Pay
- PayPal

## Documentation

- `STRIPE_INTEGRATION.md` - Guide d'intégration Stripe
- `README.md` - Ce fichier

## Installation

1. Importer `database.sql` dans MySQL
2. Configurer `includes/config.php`
3. Exécuter `update-stripe-db.php` pour Stripe
4. Configurer les clés API Stripe

## Support

Pour toute question technique, consulter la documentation.
