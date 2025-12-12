-- Creation de la database
create database salle_sport;

--Pour voir tous les databases
show databases;

--Travaille sur cette database
use salle_sport;
--creation de la table cours 
create table cours(
    id_c int auto_increment primary key,
    nom varchar(50) not null,
    catégorie varchar(50) not null,
    date_c date not null,
    heure time not null,
    durée int not null,
    nombre_m int not null
);


--creation de la table Équipement 
CREATE TABLE Équipement(
    id_é int primary key auto_increment,
    nom varchar(50) not null,
    type varchar(50) not null, 
    quantité_d varchar(50) not null, 
    état varchar(50) default 'bon'
);


-- La creation de la table associative cours_équipement
CREATE TABLE cours_équipement (
    'id_c' INT,
    'id_é' INT,
    PRIMARY KEY ('id_c', 'id_é'),
    FOREIGN KEY ('id_c') REFERENCES 'cours'('id_c'),
    FOREIGN KEY ('id_é') REFERENCES 'Équipement'('id_é')
);


CREATE TABLE personne (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);


--insere des donnees
insert into cours(nom,catégorie,date_c,heure,durée,nombre_m) 
values("Yoga","calme","2025-12-06","2025-12-02 18:26:03",02,11);


SELECT * FROM Équipement;


SELECT * FROM cours ORDER BY date_c ASC;

DELETE FROM `Équipement` WHERE id_é = $id_é;


UPDATE Équipement
SET nom='$nom', type='$type', quantite_d='$quantite_d', etat='$etat'
WHERE id_é = $id

INSERT INTO `Équipement` (nom, type, quantite_d, etat)
VALUES ('$nom', '$type', '$quantite_d', '$etat')

DELETE FROM cours WHERE id_c = ?;

UPDATE cours SET 
nom='$nom',
categorie='$categorie',
date_c='$date_c',
heur='$heur',
duree='$duree',
nombre_m='$nombre_m'
WHERE id_c = $id

SELECT COUNT(*) AS total FROM cours
