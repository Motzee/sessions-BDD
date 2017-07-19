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
$lePseudo = "Personne Dernière" ;

if(!$db->existeMembre($lePseudo)) {
    $user1 = new User($lePseudo, "membre", $sel, $mdpSale, "", "") ;
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
    $user = $db->recupererUser($pseudo) ;
    $user->connexion() ;
        echo "<p>pseudo : ".$_SESSION['pseudo']."</p>" ;
        echo "<p>id : ".$_SESSION['id']."</p>" ;
}


//récupérer un utilisateur
echo "<h1>Récupérer un utilisateur</h1>" ;
$user2 = $db->recupererUser($lePseudo) ;

echo "<pre>" ;
var_dump($user2) ;
echo "Son nom est ".$user2->getPseudo() ;
echo "</pre>" ;


//modifier un utilisateur
echo "<h1>Modifier un utilisateur</h1>" ;
$user3 = $db->recupererUser($lePseudo) ;
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
