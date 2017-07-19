<?php

namespace sessions;

class User {
    
    protected $identifiant ;
    protected $pseudo ;
    protected $statut ;
    protected $dateInscription ;
    protected $sel_hash ;
    protected $mdp_hash ;
    protected $email ;
    protected $adresse ;
    
    function __construct($pseudo, $statut, $sel_hash, $mdp_hash, $email, $adresse, $identifiant = NULL, $dateInscription = NULL) {
        $this->identifiant = $identifiant;
        $this->pseudo = $pseudo;
        $this->statut = $statut;
        $this->dateInscription = $dateInscription;
        $this->sel_hash = $sel_hash;
        $this->mdp_hash = $mdp_hash;
        $this->email = $email;
        $this->adresse = $adresse;
    }

    
//fonctions SET
    function setIdentifiant(int $identifiant) {
        $this->identifiant = $identifiant;
    }

    function setPseudo(string $pseudo) {
        $this->pseudo = $pseudo;
    }

    function setStatut(string $statut) {
        if (in_array($statut, ["membre", "modo", "admin"] )) {
            $this->statut = $statut ;
        } else {
            echo "Erreur de statut critique" ;
        }
    }

    function setDateInscription($dateInscription) {
        $this->dateInscription = $dateInscription;
    }

    function setSel_hash(string $sel_hash) {
        $this->sel_hash = $sel_hash;
    }

    function setMdp_hash(string $mdp_hash) {
        $this->mdp_hash = $mdp_hash;
    }

    function setEmail(string $email) {
        $this->email = $email;
    }

    function setAdresse(string $adresse) {
        $this->adresse = $adresse;
    }

//Fonctions GET
    
    function getIdentifiant():int {
        return $this->identifiant;
    }

    function getPseudo():string {
        return $this->pseudo;
    }

    function getStatut():string {
        if (in_array($this->statut, ["membre", "modo", "admin"] )) {
            return $this->statut;
        } else {
            echo "Erreur de statut critique" ;
        }
        
    }

    function getDateInscription() {
        return $this->dateInscription;
    }

    function getSel_hash():string {
        return $this->sel_hash;
    }

    function getMdp_hash():string {
        return $this->mdp_hash;
    }

    function getEmail():string {
        return $this->email;
    }

    function getAdresse():string {
        return $this->adresse;
    }

//fonctions de classe
 
    //génération d'un sel unique pour un utilisateur
    public static function generationSEL():string {
        $longueur = rand(10, 15) ;
        $plage = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ;
        $plagePreparee = str_shuffle($plage) ;
        $sel = substr($plagePreparee, 0, $longueur) ;
        return $sel;
    }
    
    //hachage d'un mot de passe en fonction d'un sel
    public static function hashageMDP($mdp, $sel):string {
        $mdp_sale = $mdp.$sel ;
        $mdp_cuisine = hash('sha256', $mdp_sale) ;

        return $mdp_cuisine ;
    }

//fonctions traditionnelles
    
    //connexion à une session
    public function connexion() {
        $_SESSION['pseudo'] = $this->pseudo ;
        $_SESSION['id'] = $this->identifiant ;
    }
   
    //déconnexion de la session
    public function deconnexion() {
        $_SESSION = array();
        session_destroy();
    }
    

}
