# Integration Stripe Checkout - Jolly Beauty

## Vue d'ensemble

Cette implémentation intègre Stripe Checkout avec support pour :
- Carte bancaire
- Apple Pay
- Google Pay  
- PayPal

## Fichiers créés/modifiés

### Backend
- `api/stripe-create-session.php` - API pour créer les sessions de paiement
- `includes/stripe-config.php` - Configuration sécurisée de Stripe
- `payment-success.php` - Page de succès de paiement
- `payment-cancelled.php` - Page d'annulation de paiement

### Frontend
- `checkout.php` - Page de paiement modifiée avec options Stripe

## Configuration

### Clés Stripe
- **Clé publique**: `pk_live_51PFgoYI099mQIcDd9VL1ObQ57chdKFGAG3zzpwU4sSTcd9mhCQALKuhsiAAYfrkLxUWq7T15CRu6hcFs4dt4G6Bk00hQVjoy1m`
- **Clé secrète**: `plyc-vsbe-xwxq-cthh-tepk`

Les clés sont stockées de manière sécurisée dans `includes/stripe-config.php`.

## Processus de paiement

1. **Sélection des produits** → Panier
2. **Informations client** → Formulaire checkout.php
3. **Choix du paiement**:
   - Stripe (carte, Apple Pay, Google Pay, PayPal)
   - Paiement à la livraison (option existante)
4. **Redirection Stripe** → Page de paiement sécurisée
5. **Confirmation** → Retour vers payment-success.php ou payment-cancelled.php

## Sécurité

- La clé secrète Stripe n'est jamais exposée côté client
- Validation des données côté serveur
- Vérification des sessions de paiement via API Stripe
- Protection contre les requêtes non autorisées

## Base de données

La table `orders` doit inclure les champs suivants :
- `stripe_session_id` - ID de session Stripe
- `stripe_payment_intent_id` - ID du paiement intent
- `paid_at` - Date de paiement
- `status` - Statut de la commande (pending_payment, paid, cancelled, etc.)

## Tests

Pour tester l'intégration :

1. Ajouter des produits au panier
2. Aller sur la page checkout
3. Remplir les informations de livraison
4. Sélectionner "Paiement en ligne sécurisé"
5. Cliquer sur "Payer avec Stripe"
6. Tester avec les cartes de test Stripe :
   - Carte réussie : 4242 4242 4242 4242
   - Carte échec : 4000 0000 0000 0002

## Maintenance

- Surveiller les logs d'erreurs Stripe
- Mettre à jour les clés API si nécessaire
- Vérifier les webhooks pour les notifications de paiement
- Maintenir la compatibilité avec les dernières versions de l'API Stripe

## Support

En cas de problème :
1. Vérifier les logs d'erreurs PHP
2. Consulter la documentation Stripe : https://stripe.com/docs/api
3. Vérifier la configuration des clés API
4. Tester avec différentes méthodes de paiement
