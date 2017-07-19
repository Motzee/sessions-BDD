/*On supprime la BDD si elle existe déjà*/
DROP DATABASE IF EXISTS sessions ;


CREATE DATABASE sessions ;

USE sessions ;

CREATE TABLE membres (
    id_membre INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    pseudo VARCHAR(32) NOT NULL,
    statut VARCHAR(32) NOT NULL,
    date_inscript DATE,
    sel_mdp VARCHAR(64) NOT NULL,
    hash_mdp VARCHAR(64) NOT NULL,
    email VARCHAR(64),
    adresse VARCHAR(64)
) ;

INSERT INTO membres (pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) 
	VALUES ('Administrateur', 'admin', '2017-07-17', 'TrucCompliqué', 'motSecret', 'admin@test.com', '30 petite rue, 00000 VILLE') ;


INSERT INTO membres (pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) 
	VALUES ('Modérateur', 'modo', '2017-07-18', 'TrucCompliqué2', 'motSecret2', 'modo@test.com', '31 petite rue, 00000 VILLE') ;

INSERT INTO membres (pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) 
	VALUES ('Membre1', 'membre', '2017-07-17', 'TrucCompliqué3', 'motSecret3', 'membre1@test.com', '32 petite rue, 00000 VILLE') ;

INSERT INTO membres (pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) 
	VALUES ('Membre2', 'membre', '2017-07-19', 'TrucCompliqué4', 'motSecret4', 'membre4@test.com', '33 petite rue, 00000 VILLE') ;
