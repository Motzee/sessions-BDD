<?php

namespace sessions;

class Databases {
    
    protected $bdd ;
    
    public function __construct() {
        $mdpBDD = file_get_contents("sessions/admin/accesbdd.php") ;
        
        try {
            $bdd = new \PDO('mysql:host=localhost;dbname=sessions', 'dev', $mdpBDD);
            $bdd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->bdd = $bdd ;
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    
    //CREATE
    public function ajouterUser(User $user):User {
        $pseudo = $user->getPseudo() ;
        $statut = $user->getStatut() ;
        //normalement, la date devrait se mettre seule !
        $dateInscription = date("y-m-d");
        $sel_hash = $user->getSel_hash() ;
        $mdp_hash = $user->getMdp_hash() ;
        $email = $user->getEmail() ;
        $adresse = $user->getAdresse() ;
    
        $req = $this->bdd->prepare('INSERT INTO membres(pseudo, statut, date_inscript, sel_mdp, hash_mdp, email, adresse) VALUES(:pseudo, :statut, :date_inscript, :sel_mdp, :hash_mdp, :email, :adresse)');
$req->execute(array(
            'pseudo' => $pseudo,
            'statut' => $statut,
            'date_inscript' => $dateInscription,
            'sel_mdp' => $sel_hash,
            'hash_mdp' => $mdp_hash,
            'email' => $email,
            'adresse' => $adresse        
	));

        //rajouter l'id ainsi créé à l'objet $user
        $nbId = $this->bdd->lastInsertId();
        $user->setDateInscription($dateInscription) ;
        $user->setIdentifiant($nbId) ;

        $req->closeCursor();
        return $user ;
    }
    
    //READ
    public function existeMembre(string $pseudo) {
        $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));
        
        $donnees = $reponse->fetch() ;
        
        if($donnees == false) {
            return false ;
        } else {
            return $this->recupererUser($pseudo) ;
        }
    }
    
    public function recupererUser(string $pseudo):User {

        $reponse = $this->bdd->prepare('SELECT * FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));

        $donnees = $reponse->fetch() ;
        $identifiant = $donnees['id_membre'] ;
        $statut = $donnees['statut'] ;
        $dateInscription = $donnees['date_inscript'] ;
        $sel_hash = $donnees['sel_mdp'] ;
        $mdp_hash = $donnees['hash_mdp'] ;
        $email = $donnees['email'] ;
        $adresse = $donnees['adresse'] ;
        
        $user = new User($pseudo, $statut, $sel_hash, $mdp_hash, $email, $adresse, $identifiant, $dateInscription) ;
      
        $reponse->closeCursor();
        
        return $user ;
       
    }
   
    //Update
    public function modifierUser(User $user) {
        
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
        
        echo "Modification effectuée" ;
        
        $reponse->closeCursor();
        //autre méthode qui serait mieux : 
        //modifier uniquement les champs comportant des modifications, afin d'éviter au maximum les conflits d'éditions quand deux personnes éditent un user en même temps.
    }
    
    //Delete
    public function supprimerUser(string $pseudo) {
        $reponse = $this->bdd->prepare('DELETE FROM membres WHERE pseudo = ? ');
        $reponse->execute(array($pseudo));

        echo "Membre supprimé" ;
        
        $reponse->closeCursor();
    }
}