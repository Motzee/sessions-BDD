<?php

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

$user1 = new User('Personne1', "membre", $sel, $mdpSale, "email@test.com", "42 jolie rue, 00000 VILLE") ;
$db->ajouterUser($user1) ;


//vérifier un utilisateur et son mdp
$pseudo = "Personne1" ;
$mdp_propose = "mdpx" ;

$erreur = "Identifiant ou mot de passe erroné" ;

if($db->existeMembre($pseudo) == NULL) {
    echo $erreur ;
} else {
    $userSouhaite = $db->existeMembre($pseudo) ;
    $mdpAttendu = $userSouhaite->getMdp_hash() ;
    $sel = $userSouhaite->getSel_hash() ;
    
    $mdpProposeSale = User::hashageMDP($mdp_propose, $sel) ;

    echo $mdpAttendu == $mdpProposeSale ? "personne à connecter" : $erreur ;
    
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


//supprimer un utilisateur
echo "<h1>Supprimer un utilisateur</h1>" ;
$db->supprimerUser("TopMembre") ;