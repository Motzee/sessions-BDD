<?php
session_start();


function myLoader($className) {
    $class = str_replace('\\', '/', $className) ;
    require($class.'.php') ;
}

spl_autoload_register('myLoader') ;

use sessions\User ;
use sessions\Databases ;

$db = new Databases() ;


//créer un nouvel utilisateur (ne changer que les chaines de texte)
echo "<h1>Créer un nouvel utilisateur</h1>" ;
$sel = User::generationSEL() ;
$mdpSale = User::hashageMDP("mdp", $sel) ;
$lePseudo = "Personne" ;

if(!$db->existeMembre($lePseudo)) {
    $user1 = new User('Personne', "membre", $sel, $mdpSale, "", "") ;
    $db->ajouterUser($user1) ;
} else {
    echo "Ce pseudo est déjà utilisé" ;
}



//vérifier un utilisateur et son mdp
$pseudo = "Personne" ;
$mdp_propose = "mdp" ;

$erreur = "Identifiant ou mot de passe erroné" ;

if($db->existeMembre($pseudo) == NULL) {
    echo $erreur ;
} else {
    $userSouhaite = $db->existeMembre($pseudo) ;
    $mdpAttendu = $userSouhaite->getMdp_hash() ;
    $sel = $userSouhaite->getSel_hash() ;
    
    $mdpProposeSale = User::hashageMDP($mdp_propose, $sel) ;

    if($mdpAttendu != $mdpProposeSale) {
        echo $erreur ;
    } else {
        $user = $userSouhaite ;
        $user->connexion() ;
        echo "<p>pseudo : ".$_SESSION['pseudo']."</p>" ;
        echo "<p>id : ".$_SESSION['id']."</p>" ;
    }
    
}


//récupérer un utilisateur
echo "<h1>Récupérer un utilisateur</h1>" ;
$user2 = $db->recupererUser('Personne1') ;

echo "<pre>" ;
var_dump($user2) ;
echo "Son nom est ".$user2->getPseudo() ;
echo "</pre>" ;


//modifier un utilisateur
echo "<h1>Modifier un utilisateur</h1>" ;
$user3 = $db->recupererUser('Personne1') ;
echo "<pre>" ;
var_dump($user3) ;
echo "</pre>" ;
$user3->setStatut('modo') ;
$user3->setAdresse('15 rue des gens heureux, 00000 JOLIEVILLE') ;
$db->modifierUser($user3) ;

/*
//supprimer un utilisateur
echo "<h1>Supprimer un utilisateur</h1>" ;
$db->supprimerUser("Personne1") ;
 * */

$user->deconnexion() ;
