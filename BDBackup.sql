/* Creation des concepts 

NB : Arme specialise militaire
	 Militaire generalise Armes

*/

/* On vide les Tables */

TRUNCATE TABLE Concept;
TRUNCATE TABLE TermeVedette;
TRUNCATE TABLE Terme;

/* On commence l'insertion */

/* UPDATE Concept c SET c.generalise = TabConcept_t(SELECT REF(c2) FROM Concept c2 Where c2.nomConcept = 'Armes') WHERE c.nomConcept = 'Militaire'; */

INSERT INTO Concept VALUES('Militaire','Description Militaire', TabConcept_t(),TabConcept_t());
INSERT INTO Concept VALUES('Armes', 'Objet permettant d attaquer ou se defendre', TabConcept_t(), TabConcept_t());
INSERT INTO Concept VALUES('Vehicules', 'Permet le deplacement d unites', TabConcept_t(), TabConcept_t());
INSERT INTO Concept VALUES('Soldats', 'Differents types de soldats', TabConcept_t(), TabConcept_t());

INSERT INTO TermeVedette VALUES('Militaire', 'Description Militaire', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Militaire'));
INSERT INTO TermeVedette VALUES('Armes', 'Objet permettant d attaquer ou se defendre', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Armes'));
INSERT INTO TermeVedette VALUES('Vehicules', 'Permet le deplacement d unites', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Vehicules'));
INSERT INTO TermeVedette VALUES('Soldats', 'Differents types de soldats', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Soldats'));

INSERT INTO TermeVedette VALUES('Military', 'Military Description', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Militaire'));
INSERT INTO TermeVedette VALUES('Weapon', 'Objet can use to fire motherfucker', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Armes'));
INSERT INTO TermeVedette VALUES('Cars', 'Move with it', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Vehicules'));
INSERT INTO TermeVedette VALUES('Soldiers', 'many men of peace', TabTerme_t(), TabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE nomConcept = 'Soldats'));

INSERT INTO Terme VALUES ('Cavalier', 'Soldat a cheval', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Fantassin', 'Soldat a pied', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Marin', 'Soldat sur bateau', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Aviateur', 'Pilote d avion', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Artilleur', 'Soldat a main de l ancien temps', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Canonnier', 'Soldat qui porte un canon', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Garde', 'Soldat qui fait le guet', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Milicien', 'Soldat des armees rebelles', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Patrouilleur', 'Soldat qui patrouille une zone', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Recruteur', 'Soldat recrutant les gens', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());

INSERT INTO Terme VALUES ('Jeep', 'Vehicule leger de transport', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Tank', 'Engin a chenilles de combat', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Bateau', 'Se deplace sur la mer', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Char', 'Plus gros que le tank, implique plus de degats', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Helicoptere', 'Permet le deplacement rapide d unites', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Parachute', 'Permet l envoi de soldats sur le terrain', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Transporteur', 'Une Jeep beaucoup plus grande', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Voiture', 'Vehicule a 4 roues', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Moto', 'Vehicule a 2 roues', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());

INSERT INTO Terme VALUES ('Fusil', 'Arme non automatique', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Mitrailette', 'Arme automatique de combat semi distance', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Fusil a pompe', 'Arme pour les combats rapproches', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Pistolet', 'Si plus de munitions dans l arme principale', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Akimbo', 'Double pistolet', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Grenade', 'Ca fait boooom!!!', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Bazooka', 'Ca fait ENCORE PLUS boom !!!!', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Bombe', 'On la depose pour vite courir', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());
INSERT INTO Terme VALUES ('Lance Flammes', 'FIRRRRRRRRRRRRREEEEEEEEE !', TabTerme_t(), TabTerme_t(), TabTermeVedette_t());

/*Ajout des concepts plus généraux*/
INSERT INTO TABLE ( SELECT c.generalise FROM Concept c WHERE c.NomConcept='Militaire')
VALUES ( (SELECT REF(c2) FROM CONCEPT c2 WHERE c2.nomConcept='Soldats' ) );
INSERT INTO TABLE ( SELECT c.generalise FROM Concept c WHERE c.NomConcept='Armes')
VALUES ( (SELECT REF(c2) FROM CONCEPT c2 WHERE c2.nomConcept='Vehicules' ) );

/*Ajout des concepts plus spécialisés*/
INSERT INTO TABLE ( SELECT c.specialise FROM Concept c WHERE c.NomConcept='Soldats')
VALUES ( (SELECT REF(c2) FROM CONCEPT c2 WHERE c2.nomConcept='Militaire' ) );
INSERT INTO TABLE ( SELECT c.specialise FROM Concept c WHERE c.NomConcept='Vehicules')
VALUES ( (SELECT REF(c2) FROM CONCEPT c2 WHERE c2.nomConcept='Armes' ) );

/*Ajout des termes associés : RELATION BIDIRECTIONNELLE : DANS LES DEUX SENS*/
/*De Militaire aux autres termes vedettes*/
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Militaire')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Militaire' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Militaire')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Militaire' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Militaire')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Militaire' ) );
/*De Armes aux armes*/
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Fusil' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Fusil')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Mitrailette' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Mitrailette')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Fusil a pompe' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Fusil a pompe')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Pistolet' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Pistolet')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Akimbo' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Akimbo')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Grenade' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Grenade')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Bazooka' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Bazooka')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Bombe' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Bombe')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Lance Flammes' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Lance Flammes')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );
/*De véhicules aux véhicules*/
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Jeep' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Jeep')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Tank' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Tank')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Bateau' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Bateau')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Char' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Char')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Helicoptere' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Helicoptere')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Parachute' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Parachute')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Transporteur' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Transporteur')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Voiture' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Voiture')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Moto' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Moto')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );
/*De Soldats aux soldats*/
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Cavalier' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Cavalier')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Fantassin' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Fantassin')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Marin' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Marin')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Aviateur' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Aviateur')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Artilleur' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Artilleur')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Canonnier' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Canonnier')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Garde' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Garde')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Milicien' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Milicien')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Patrouilleur' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Patrouilleur')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
INSERT INTO TABLE ( SELECT t.associe FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TERME t2 WHERE t2.nomTerme='Recruteur' ) );
INSERT INTO TABLE ( SELECT t.associe FROM Terme t WHERE t.nomTerme='Recruteur')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );
/*Flemme de faire de terme à terme (Ce sera pour tester les up via les formulaires*/

/*Ajout des termes traduits ATTENTION : TRADUCTION LIMITEE AUX TERMES VEDETTE*/
INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Militaire')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Military' ) );
INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Military')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Militaire' ) );

INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Armes')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Weapon' ) );
INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Weapon')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Armes' ) );

INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Vehicules')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Cars' ) );
INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Cars')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Vehicules' ) );

INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Soldats')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldiers' ) );
INSERT INTO TABLE ( SELECT t.traduit FROM TermeVedette t WHERE t.nomTerme='Soldiers')
VALUES ( (SELECT REF(t2) FROM TermeVedette t2 WHERE t2.nomTerme='Soldats' ) );


/*Ajout des termes synonymes (au pif)*/
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Artilleur')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Canonnier' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Canonnier')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Artilleur' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Fantassin')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Milicien' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Milicien')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Fantassin' ) );

INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Tank')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Char' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Char')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Tank' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Jeep')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Voiture' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Voiture')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Jeep' ) );


INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Fusil')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Pistolet' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Pistolet')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Fusil' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Grenade')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Bombe' ) );
INSERT INTO TABLE ( SELECT t.synonymes FROM Terme t WHERE t.nomTerme='Bombe')
VALUES ( (SELECT REF(t2) FROM Terme t2 WHERE t2.nomTerme='Grenade' ) );
