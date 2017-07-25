<?php

namespace sessions ;

class DbUsers {
    
    protected $bdd ;
    
    public function __construct() {
        $ini_array = parse_ini_file("sessions/admin/admin.ini");

        $chemin = 'mysql:host='.$ini_array['host'].';dbname='.$ini_array['dbname'] ;
        
        try {
            $bdd = new \PDO($chemin, $ini_array['username'], $ini_array['passwrd']);
            $bdd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->bdd = $bdd ;
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    
    //CREATE
    public function ajouterUser(User $user):User {
        $dateInscription = date("y-m-d");
        $user->getStatut() ;
    
        $req = $this->bdd->prepare('INSERT INTO membres(pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) VALUES(:pseudo, :statut, :date_inscript, :sel_mdp, :hash_mdp, :email, :adresse)');
        
        $req->bindValue('pseudo', $user->getPseudo());
        $req->bindValue('statut', $user->getStatut());
        $req->bindValue('date_inscript', $dateInscription);
        $req->bindValue('sel_mdp', $user->getSel_hash());
        $req->bindValue('hash_mdp', $user->getMdp_hash());
        $req->bindValue('email', $user->getEmail());
        $req->bindValue('adresse', $user->getAdresse());
        
        $req->execute();

        //rajouter l'id ainsi créé à l'objet $user
        $nbId = $this->bdd->lastInsertId();
        $user->setDateInscription($dateInscription) ;
        $user->setIdentifiant($nbId) ;

        $req->closeCursor();
        
        return $user ;
    }
    

    
    //READ
    public function existeUser($personne):bool {
        if (is_int($personne)) {
            $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE id_membre = ? ');
        } elseif(is_string($personne)) {
            $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? ');
        } else {
            return false ;
        }
        
        $reponse->execute(array($personne));
        
        $donnees = $reponse->fetch() ;
        
        $reponse->closeCursor();
        
        return $donnees == false ? false : true ;
    }
    
    protected function creerUser(array $donnees):User {
        $identifiant = $donnees['id_membre'] ;
        $pseudo = $donnees['pseudo'] ;
        $statut = $donnees['statut'] ;
        $dateInscription = $donnees['date_inscript'] ;
        $sel_hash = $donnees['sel_mdp'] ;
        $mdp_hash = $donnees['hash_mdp'] ;
        $email = $donnees['email'] ;
        $adresse = $donnees['adresse'] ;
        
        $user = new User($pseudo, $statut, $sel_hash, $mdp_hash, $email, $adresse, $identifiant, $dateInscription) ;
        return $user ;
    }
    
    public function recupererID(string $pseudo):int {
        $reponse = $this->bdd->prepare('SELECT id_membre FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));

        $donnees = $reponse->fetch() ;
      
        $reponse->closeCursor();
        
        return $donnees['id_membre'] ;
    }
    
    public function recupererUser(int $id):User {

        $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE id_membre = ? ');
        $reponse->execute(array($id));

        $donnees = $reponse->fetch() ;
        
        $user = $this->creerUser($donnees) ;
      
        $reponse->closeCursor();
        
        return $user ;
       
    }
    
    public function recupererListeUsers():array {
        $reponse = $this->bdd->query('SELECT id_membre FROM membres');
        
        $listeUsers = [] ;
        
        while ($donnees = $reponse->fetch()) {
            $listeUsers[] = $this->recupererUser($donnees['id_membre']) ;
        }
        
        $reponse->closeCursor();
        
        return $listeUsers ;
    }
    
    public function loginUser(string $pseudo, string $mdpPropose):bool {
        if(!$this->existeUser($pseudo)) {
            return false ;
        } else {
            $reponse = $this->bdd->prepare('SELECT sel_mdp FROM membres WHERE pseudo = ? ');
            $reponse->execute(array($pseudo));
        
            $donnees = $reponse->fetch() ;
            
            $sel = $donnees['sel_mdp'] ;
            
            $reponse->closeCursor();
            
            $mdpProposeSale = User::hashageMDP($mdpPropose, $sel) ; 
                    
            //chercher à récupérer l'entrée WHERE pseudo AND mdphashé, et on retourne true ou false selon
            $reponse2 = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? AND hash_mdp = ? ');
            $reponse2->execute(array($pseudo, $mdpProposeSale));

            $donnees = $reponse2->fetch() ;

            return $donnees === false ? false : true ;
            
        }
    }
   
    //Update
    public function modifierUser(User $user):bool {
        
        $reponse = $this->bdd->prepare('UPDATE membres SET pseudo = :pseudo, statut = :statut, date_inscript = :date_inscript, sel_mdp = :sel_mdp, hash_mdp = :hash_mdp, email = :email, adresse = :adresse WHERE id_membre = :id_membre');  
        $reponse->execute(array(
            'pseudo' => $user->getPseudo(),
            'statut' => $user->getStatut(),
            'date_inscript' => $user->getDateInscription(),
            'sel_mdp' => $user->getSel_hash(),
            'hash_mdp' => $user->getMdp_hash(),
            'email' => $user->getEmail(),
            'adresse' => $user->getAdresse(),
            'id_membre' => $user->getIdentifiant()
	));

        return $reponse->rowCount() == 1 ? true : false ;
        //autre méthode qui serait mieux : 
        //modifier uniquement les champs comportant des modifications, afin d'éviter au maximum les conflits d'éditions quand deux personnes éditent un user en même temps.
    
    }
    
    //DELETE
    public function supprimerUser(int $id):bool {

            $reponse = $this->bdd->prepare('DELETE FROM membres WHERE id_membre = ? ');
            
            if($reponse->execute(array($id)) && $reponse->rowCount() == 1) {
                $reponse->closeCursor();
                return true;
            } else {
                $reponse->closeCursor();
                return false ;
            }
    }
}
