#Utilisation :

Pour utiliser un Objet CrudConcept ou CrudTerme ou CrudTermeVedette il faut l'apeller, ce sont des services :

$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');

$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');

$crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');

#CrudConcept :

- findByNom($nom) : Prend en argument une chaine de caractère et retourne un tableau contenant des objets
Concept pour lesquels $nom est egal ou contenu dans nomConcept.
- getByNom($nom) : Prend en argument une chaine de caractère et retourne l'unique Concept dont $nom = nomConcept
- update($concept) : actualise un Concept en BDD; Utile pour ajouter des Concepts parents ou 
fils dans le tab aprés modification des objets. Il faut penser que si on ajoute un parents à un concept
alors il faudra ajouter un fils dans le parents.. (donc 2 objet modifié et 2 update($concept) )
Penser égaement à créer le concept si il n'éxiste pas encore, la fonction se servira des OID pour les références.
- creer($concept) créer un concept en BDD.
- supprimer($concept) : Je dois vraiment l'expliquer ? xD

#CrudTerme :

Ces fonctions peuvent renvoyer des "Terme" et des "TermesVedette" donc penser à utiliser "get_class($terme)" 
pour savoir si il est vedette ou non.

- findByNom($nom) : Renvoi un tableau avec la liste des Termes et TermeVedette trouvé.
- getByNom($nom) : Retourne un tableau avec en indice 0 le Terme et
la suite des indices correspondent aux objets TermeVedette auquel il est synonymes.
- update($terme) : Même principe que pour un concept.
Attention si on change un élément d'un tab (généralise, spécialise, Synonyme) penser à le changer
dans le Tab de lautre élément.. et donc faire encore une fois 2 update($terme) comme pour un concept.
- creer($terme)
- supprimer($terme)

#CrudTermeVedette :

Hérite de CrudTerme

- getByConcept($concept) : retourne le TermeVedette Associé à un Concept.
- creer($concept)
- update($terme)
- supprimer($terme)
