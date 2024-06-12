# 📚 Bibliothèque en ligne de commande

Bienvenue dans le projet de gestion de bibliothèque en ligne ! Ce script PHP permet de gérer une collection de livres, en proposant plusieurs fonctionnalités accessibles via une interface en ligne de commande.

## 🚀 Fonctionnalités
- Création de livre : Ajouter un livre avec un nom, une description et sa disponibilité en stock.
- Modification d’un livre : Modifier les détails d'un livre existant.
- Suppression d’un livre : Supprimer un livre de la collection.
- Affichage de tous les livres : Voir la liste complète des livres.
- Affichage d’un livre : Voir les détails d’un livre spécifique.
- Tri des livres : Trier les livres par nom, description ou disponibilité.
- Recherche d’un livre : Rechercher un livre spécifique par nom, description, disponibilité ou ID.
- Historique des actions : Consulter l'historique des actions effectuées.

## 📦 Installation
Clonez le dépôt :
```bash
git clone https://github.com/votre-utilisateur/votre-repo.git
```
Accédez au répertoire du projet :
```bash
cd votre-repo
```
Assurez-vous que le fichier data.json est présent dans le répertoire racine du projet.

## 🛠️ Utilisation
Exécutez le script PHP via la ligne de commande :
```bash
php script.php
```
Suivez les instructions du menu pour naviguer à travers les différentes fonctionnalités.

## Exemple d'Utilisation
```text
Menu:
1. Création de livre
2. Modification d’un livre
3. Suppression d’un livre 
4. Affichage de tout les livres 
5. Affichage d’un livre 
6. Tri des livres 
7. Recherche d’un livre 
8. Voir l'historique 
9. Quitter
Entre votre choix:
```

## 🗃️ Format de stockage
Les livres et l'historique sont stockés dans un fichier data.json au format suivant :
```json
{
    "books": [
        {
            "id": 1,
            "nom": "Nom du livre",
            "description": "Description du livre",
            "disponible": "disponible"
        }
    ],
    "history": [
        "Action effectuée"
    ]
}
```

## 📝 Historique
Chaque action (ajout, modification, suppression, tri, recherche) est enregistrée dans l'historique et peut être consultée via l'option du menu correspondante.


