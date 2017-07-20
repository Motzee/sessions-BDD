<?php

/*Partie à intégrer au début de chaque fichier*/
session_start();

function myLoader($className) {
    $class = str_replace('\\', '/', $className) ;
    require_once($class.'.php') ;
}

spl_autoload_register('myLoader') ;

$db = new sessions\DbUsers() ;

if(isset($_SESSION['id']) && $_SESSION['id'] != NULL) {
    $user = $db->recupererUser($_SESSION['id']) ;
}

/*fin de la Partie à intégrer au début de chaque fichier*/



//créer un nouvel utilisateur (ne changer que les chaines de texte)
echo "<h1>Créer un nouvel utilisateur</h1>" ;
$sel = sessions\User::generationSEL() ;
$mdpSale = sessions\User::hashageMDP("mdp", $sel) ;
$lePseudo = "Personne Dernière" ;

if(!$db->existeUser($lePseudo)) {
    $user1 = new sessions\User($lePseudo, "membre", $sel, $mdpSale, "", "") ;
    $db->ajouterUser($user1) ;
} else {
    echo "Ce pseudo est déjà utilisé" ;
}


//vérifier un utilisateur et son mdp
$pseudo = "PersonneX";
$mdp_propose = "mdp" ;

if(!$db->loginUser($pseudo, $mdp_propose)) {
    echo "Identifiant ou mot de passe erroné" ;
} else {
    $iduser = $db->recupererID($pseudo) ;
    $user = $db->recupererUser($iduser) ;
    $user->connexion() ;
        echo "<p>pseudo : ".$_SESSION['pseudo']."</p>" ;
        echo "<p>id : ".$_SESSION['id']."</p>" ;
}


//récupérer un utilisateur
echo "<h1>Récupérer un utilisateur</h1>" ;
    $pseudo2 = "PersonneX" ;
    $iduser2 = $db->recupererID($pseudo2) ;
    $user2 = $db->recupererUser($iduser2) ;

echo "<pre>" ;
var_dump($user2) ;
echo "Son nom est ".$user2->getPseudo() ;
echo "</pre>" ;


//modifier un utilisateur
echo "<h1>Modifier un utilisateur</h1>" ;
$iduser3 = $db->recupererID($lePseudo) ;
    $user3 = $db->recupererUser($iduser3) ;

echo "<pre>" ;
var_dump($user3) ;
echo "</pre>" ;
$user3->setStatut('modo') ;
$user3->setAdresse('17 rue des gens très heureux, 00000 JOLIEVILLE') ;

echo $db->modifierUser($user3) ? "Modification effectuée" : "Une erreur de modification (aucune, ou non prise en compte) a été rencontrée" ;


//supprimer un utilisateur
echo "<h1>Supprimer un utilisateur</h1>" ;

echo $db->supprimerUser("MembreLeRetourBis") ? "Membre supprimé" : "Problème de suppression" ;


$user->deconnexion() ;

$pseudo1 = "PersonnetX" ;

if($db->existeUser($pseudo1)) {
    echo "<p>L'utilisateur $pseudo1 existe</p>" ;
} else {
    echo "<p>L'utilisateur $pseudo1 n'existe pas<p>" ;
}

$pseudo3 = "personneX" ;

if($db->existeUser($pseudo3)) {
    echo "<p>L'utilisateur $pseudo3 existe</p>" ;
} else {
    echo "<p>L'utilisateur $pseudo3 n'existe pas<p>" ;
}

echo $db->recupererID('PersonneX') ;