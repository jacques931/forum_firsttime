# Forum PHP avec MongoDB

Ce projet est un forum interactif construit en PHP et utilisant MongoDB comme base de données. Il met en œuvre plusieurs fonctionnalités avancées tout en démontrant mes compétences en développement backend avec PHP et l'intégration de MongoDB.

> **Note :** Ce projet a été réalisé dans le cadre d'un projet scolaire pour démontrer mes compétences techniques et pratiques en développement web.

## Fonctionnalités principales

### Gestion des utilisateurs
- Inscription et connexion des utilisateurs.
- Page de profil avec la possibilité de mettre à jour ses informations personnelles.
- Déconnexion.

### Gestion des posts et commentaires
- Création de posts par les utilisateurs connectés.
- Ajout de commentaires sur les posts.
- Réponses imbriquées aux commentaires, permettant de créer des fils de discussion.
- Modification et suppression des posts et des commentaires (avec gestion des réponses associées).
- Plusieurs modes d'affichage des commentaires :
  - Chronologique (du plus récent au plus ancien et inversement).
  - En fils de discussion ou imbriqués.

### Statistiques
- Nombre total de posts, de commentaires et d'utilisateurs visibles sur la page d'accueil.
- Nombre de commentaires par post.
- Nombre d'utilisateurs ayant interagi avec un post.
- Statistiques personnelles sur la page de profil (nombre de posts et commentaires créés, interactions sur les posts de l'utilisateur).

### Page d'accueil
- Affichage de tous les posts avec pagination (10 posts par page).
- Liste des 10 derniers commentaires avec lien direct vers les posts associés.

### Expérience utilisateur améliorée
- Affichage des dates en format relatif (ex. : "il y a 3 heures", "à l'instant").
- Intégration de JavaScript pour toggler les formulaires de commentaires et de réponses.

## Technologie utilisée

### Backend
- **PHP** : Utilisation des bonnes pratiques de programmation, incluant la gestion des sessions et la validation des entrées utilisateur.

### Base de données
- **MongoDB** :
  - Structure NoSQL pour gérer les données des utilisateurs, des posts, et des commentaires.
  - Utilisation des collections imbriquées pour optimiser les réponses et les fils de discussion.

### Frontend
- **HTML, CSS, JavaScript** :
  - Interface conviviale et adaptative.
  - Utilisation de JavaScript pour des interactions dynamiques (comme le toggle des formulaires).

## Démonstration des compétences

- **Architecture backend** : J'ai conçu une application modulaire avec un code PHP organisé et maintenable.
- **Gestion NoSQL** : Intégration et manipulation des données avec MongoDB pour répondre à des cas d'utilisation complexes.
- **Développement frontend** : Création d'une interface utilisateur fluide et réactive.
