<?php

namespace sessions;

class Databases {
    
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
        //sécurisation des variables (mais qui ne fonctionne pas) - attention à bindParam et bindValue
        $req->bindValue('pseudo', $user->getPseudo(), \PDO::PARAM_STR);
        $req->bindValue('statut', $user->getStatut(), \PDO::PARAM_STR);
        $req->bindValue('date_inscript', $dateInscription, \PDO::PARAM_INT);
        $req->bindValue('sel_mdp', $user->getSel_hash(), \PDO::PARAM_STR);
        $req->bindValue('hash_mdp', $user->getMdp_hash(), \PDO::PARAM_STR);
        $req->bindValue('email', $user->getEmail(), \PDO::PARAM_STR);
        $req->bindValue('adresse', $user->getAdresse(), \PDO::PARAM_STR);
        //On exécute la requête préparée
        $req->execute();

        //rajouter l'id ainsi créé à l'objet $user
        $nbId = $this->bdd->lastInsertId();
        $user->setDateInscription($dateInscription) ;
        $user->setIdentifiant($nbId) ;

        $req->closeCursor();
        return $user ;
    }
    
    //READ
    public function existeMembre(string $pseudo):bool {
        $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));
        
        $donnees = $reponse->fetch() ;
        
        return $donnees == false ? false : true ;
    }
    
    protected function creerUser($pseudo, array $donnees) {
        $identifiant = $donnees['id_membre'] ;
        $statut = $donnees['statut'] ;
        $dateInscription = $donnees['date_inscript'] ;
        $sel_hash = $donnees['sel_mdp'] ;
        $mdp_hash = $donnees['hash_mdp'] ;
        $email = $donnees['email'] ;
        $adresse = $donnees['adresse'] ;
        
        $user = new User($pseudo, $statut, $sel_hash, $mdp_hash, $email, $adresse, $identifiant, $dateInscription) ;
        return $user ;
    }
    
    public function recupererUser(string $pseudo):User {

        $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));

        $donnees = $reponse->fetch() ;
        
        $user = $this->creerUser($pseudo, $donnees) ;
      
        $reponse->closeCursor();
        
        return $user ;
       
    }
    
    public function loginUser(string $pseudo, string $mdpPropose) {
        if(!$this->existeMembre($pseudo)) {
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

            return $donnees == false ? false : true ;
            
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
    
    //Delete
    public function supprimerUser(string $pseudo):bool {

            $reponse = $this->bdd->prepare('DELETE FROM membres WHERE pseudo = ? ');
            
            if($reponse->execute(array($pseudo)) && $reponse->rowCount() == 1) {
                return true;
            } else {
                return false ;
            }
    }
}