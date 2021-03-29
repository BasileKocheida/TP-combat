<?php

class PersonnagesManager{
    private $_db;

    public function __construct($db){
        $this->setDb($db);
    }

    public function add(Personnage $perso){
        // Préparation de la requête d'insertion.
        $q = $this->_db->prepare('INSERT INTO personnages(nom,type) VALUES(:nom, :type)');
        // Assignation des valeurs pour le nom du personnage.
        $q->bindValue(':nom', $perso->nom());
        $q->bindValue(':type', $perso->type());
        // Exécution de la requête.
        $q->execute();
        
        // Hydratation (initialisation des paramètres) du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
        $perso->hydrate([
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ]);
    }
  
    public function count(){
        // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }
  
    public function delete(Personnage $perso){
        // Exécute une requête de type DELETE.
        $this->_db->exec('DELETE FROM personnages WHERE id='.$perso->id());
    }
  
    public function exists($info){
        // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
        if (is_int($info)){
            // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
            return (bool) $this->_db->query('SELECT COUNT(*)FROM personnages WHERE id='.$info)->fetchColumn();
        }
            // Sinon c'est qu'on a passé un nom.
            $q= $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
            // Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.
            $q->execute([':nom' => $info]);
        return (bool) $q->fetchColumn();
    
    }
  
    public function get($info){
        // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
        if (is_int($info)){
            // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
            $q = $this->_db->query('SELECT id, nom, degats, type FROM personnages WHERE id='.$info);
            $perso = $q->fetch(PDO::FETCH_ASSOC);

        }else{// Sinon, on veut récupérer le personnage avec son nom.
            // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
            $q = $this->_db->prepare('SELECT id, nom, degats, type FROM personnages WHERE nom = :nom');
            $q->execute([':nom' => $info]);
            $perso = $q->fetch(PDO::FETCH_ASSOC);

        }
        switch (ucfirst($perso['type'])) {
            case 'Guerrier':
               return new Guerrier($perso);
                break;
            case 'Magicien':
                return new Magicien($perso);
                break;
            case 'Archer':
                return new Archer($perso);
                break;
        }
        
        
    
    
    }
   
  
    public function getList($nom){
        // Retourne la liste des personnages dont le nom n'est pas $nom.
        $persos = [];

        $q = $this->_db->prepare('SELECT id, nom, degats, type FROM personnages WHERE nom != :nom ORDER BY nom');
        $q->execute([':nom' => $nom]);
        
        // Le résultat sera un tableau d'instances de Personnage.
        while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
            switch (ucfirst($donnees['type'])){
                case 'Guerrier': $persos[] = new Guerrier($donnees); break;
                case 'Magicien': $persos[] = new Magicien($donnees); break;
                case 'Archer': $persos[] = new Archer($donnees); break;
            }
        }
        
        return $persos;

    }

    public function update(Personnage $perso){
        // Prépare une requête de type UPDATE.
        $q = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');

        // Assignation des valeurs à la requête.
        $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
        $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
        
        // Exécution de la requête.
        $q->execute();
    }
    
    public function setDb(PDO $db){
        $this->_db = $db;
    }

}
    