DROP TABLE Concept;
DROP TABLE TermeVedette;
DROP TABLE Terme;

DROP TYPE TermeVedette_t FORCE;
DROP TYPE Terme_t FORCE;
DROP TYPE Concept_t FORCE;
DROP TYPE TabTerme_t FORCE;
DROP TYPE TabConcept_t FORCE;
DROP TYPE TabTermeVedette_t FORCE;


/* ---------- Création des types ---------- */


CREATE TYPE Concept_t;
/

CREATE TYPE TabConcept_t AS TABLE OF REF Concept_t;
/

CREATE TYPE Terme_t;
/

CREATE TYPE TermeVedette_t;
/

CREATE TYPE TabTermeVedette_t AS TABLE OF REF TermeVedette_t;
/
CREATE TYPE TabTerme_t AS TABLE OF REF Terme_t;
/

CREATE OR REPLACE TYPE Terme_t AS OBJECT
(
  nomTerme VARCHAR(30),
	description VARCHAR(200),
  associe     TabTerme_t,
  traduit     TabTerme_t,
  synonymes   TabTermeVedette_t
) NOT FINAL;
/

CREATE OR REPLACE TYPE TermeVedette_t UNDER Terme_t
(
  concept REF Concept_t
);
/


CREATE OR REPLACE TYPE Concept_t FORCE AS OBJECT
(
  nomConcept VARCHAR(30),
	description VARCHAR(200),
  generalise  TabConcept_t,
  specialise  TabConcept_t
);
/


/* ---------- Création des tables ---------- */


CREATE TABLE Concept OF Concept_t
(
  CONSTRAINT pk_Concept PRIMARY KEY(nomConcept),
  CONSTRAINT NotNullNomConcept_Concept CHECK(nomConcept IS NOT NULL)
)
NESTED TABLE generalise STORE AS listeGeneralise,
NESTED TABLE specialise STORE AS listeSpecialise;
/

CREATE TABLE Terme OF Terme_t
(
  CONSTRAINT pk_Terme PRIMARY KEY(nomTerme),
  CONSTRAINT NotNullNomTerme_Terme CHECK(nomTerme IS NOT NULL),
  CONSTRAINT NotNullDescription_Terme CHECK(description IS NOT NULL)
)
NESTED TABLE traduit STORE AS listeTraduction,
NESTED TABLE associe STORE AS listeAssociation,
NESTED TABLE Synonymes STORE AS listeSynonymes;
/

CREATE TABLE TermeVedette OF TermeVedette_t
(
  CONSTRAINT pk_TermeVedette PRIMARY KEY(nomTerme),
  CONSTRAINT NotNullNomTerme_TermeV CHECK(nomTerme IS NOT NULL),
  CONSTRAINT NotNullDescription_TermeV CHECK(description IS NOT NULL),
  CONSTRAINT NotNullConcept_TermeV CHECK(concept IS NOT NULL)
)
NESTED TABLE traduit STORE AS listeTraductionVedette,
NESTED TABLE associe STORE AS listeAssociationVedette,
NESTED TABLE Synonymes STORE AS listeSynonymesVedette;
/