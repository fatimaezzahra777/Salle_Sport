# Salle_Sport
# Plateforme de Gestion d’une Salle de Sport
 Description du projet

Cette plateforme permet de gérer les cours, les équipements, ainsi que d'afficher un tableau de bord récapitulatif pour une salle de sport. Elle facilite le suivi, la gestion et l’organisation des activités.

 Objectifs

Centraliser les informations liées aux cours et équipements.

Faciliter la planification et le suivi quotidien.

Offrir une vue d’ensemble claire grâce au tableau de bord.

 Fonctionnalités principales
 Gestion des cours

Ajouter, modifier et supprimer un cours.

Choisir la catégorie, la date, l’heure, la durée et la capacité.

Associer des équipements si nécessaires.

 Gestion des équipements

Ajouter un équipement.

Modifier/supprimer un équipement.

Indiquer l’état et la disponibilité.

 Tableau de bord

Nombre total de cours.

Nombre total d’équipements.

Statistiques visuelles (graphiques).

 Modélisation (ERD)

Un diagramme ERD illustre la relation :

Cours ⟷ Équipements via une table associative (cours_equipement).

La table associative contient uniquement :

cours_id

equipement_id

(Diagramme fourni séparément ou généré dans le projet.)

 Technologies utilisées

Frontend : HTML, CSS (TailwindCSS), JavaScript

Backend : PHP

Base de données : MySQL

 Démonstration du fonctionnement

L’utilisateur accède à l’interface.

Il peut consulter la liste des cours et équipements.

Il peut ajouter/modifier des données via des formulaires simples.

Le tableau de bord se met à jour automatiquement.