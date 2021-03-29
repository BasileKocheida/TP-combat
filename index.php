<?php

// On enregistre notre autoload.
function chargerClasse($classname){
  require "class/". $classname.'.php';
}

spl_autoload_register('chargerClasse');

session_start(); // On appelle session_start() APRÈS avoir enregistré l'autoload.

if (isset($_GET['deconnexion'])){
  session_destroy();
  header('Location: .');
  exit();
}


$db = new PDO('mysql:host=127.0.0.1;dbname=combat', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.

$manager = new PersonnagesManager($db);

include 'combat.php';


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link href="custom.css?v=<?php echo time(); ?>" rel="stylesheet">
    <title>POO fight</title>
</head>


<body>

 <a href="?deconnexion=1"><button type="button" class="btn btn-danger">Déconnexion</button></a>

<p>Nombre de personnages créés : <?= $manager->count() ?></p>
<?php
if (isset($message)){ // On a un message à afficher ?
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}
  if (isset($perso)){ // Si on utilise un personnage (nouveau ou pas).

?>
  
    <fieldset>
      <legend>Mes informations</legend>
      <p>
        Nom : <?= htmlspecialchars($perso->nom()) ?><br />
        Dégâts : <?= $perso->degats() ?><br />
        Type : <?= $perso->type() ?>
      </p>
<div class="afficher">
          <div class="card <?php if ($perso->type() == 'guerrier'){
              echo 'bg-danger'; }else if ($perso->type() == 'magicien'){
              echo 'bg-primary'; }else if ($perso->type() == 'archer'){
              echo 'bg-success';} ?>">
            
                <div class="card-body">
                  <h5 class="card-title">Hero skills</h5>
                  <h3 class="card-hero-name"><?= htmlspecialchars($perso->nom()) ?> </h3>
                  <p class="card-text">
                    Hero description <br>  Dégâts : <?= $perso->degats() ?><br/> Type : <?= $perso->type() ?> </p>
                </div>
              </div>
  </div>
    </fieldset>

    <fieldset>
      <legend>Qui frapper ?</legend>
<div class="afficher">   
    <p> <?php


      $persos = $manager->getList($perso->nom());
     

      if (empty($persos)){
        echo 'Personne à frapper !';
      }

      else{
        foreach ($persos as $unPerso) {

          ?>
          <div class="card
          <?php if ($unPerso->type() == 'guerrier'){
              echo 'bg-danger'; }else if ($unPerso->type() == 'magicien'){
              echo 'bg-primary'; }else{
              echo 'bg-success';} ?>">

                      <div class="card-body">
                          <p class="card-text"><a class="stylea btn btn-secondary" href="?frapper= <?= $unPerso->id() ?>"> <?= htmlspecialchars($unPerso->nom()) ?></a> (dégâts : <?= $unPerso->degats() ?> | type : <?= $unPerso->type()?>)</p>

                      </div>
                  </div>

          <br />
          <?php
        }
        
      }
      ?>
    </p>
 
          </fieldset>
        
  </div>
  <?php
      }else{
  ?>

  <div class="container-title">
    <h1><span class="fight">POO Fight</span> <span class="club">Club</span></h1>
  </div>
 <div class='container'> 
  <div class="rules">
    <h5>Règles :</h5>
    <p>
    La première règle du POO Fight Club est : il est interdit de parler du POO Fight Club. <br> 
    La seconde règle du POO Fight Club est : il est interdit de parler du POO Fight Club. <br>
    Troisième règle du POO Fight Club : quelqu'un crie stop, quelqu'un s'écroule ou n'en peut plus, le combat est terminé<br>
    Quatrième règle : seulement deux hommes par combat. <br>
    Cinquième règle : un seul combat à la fois, messieurs.<br> 
    Sixième règle : pas de chemise, ni de chaussures. <br>
    Septième règle : les combats continueront aussi longtemps que nécessaire. <br>
    Et huitième et dernière règle : si c'est votre première soirée au POO Fight Club, vous devez vous battre.
    </p>
  </div>
  <div class="container-form">
      <form action="" method="post">
          <p>
            Nom : <input type="text" name="nom" maxlength="50" style="margin-bottom: 10px;"/><br>
            Type :
          <select name="type" style="margin-bottom: 10px;">
            <option value="Guerrier">Guerrier</option>
            <option value="Magicien">Magicien</option>
            <option value="Archer">Archer</option>
          </select><br>
            <input type="submit" value="Créer ce personnage" name="creer" style="margin-bottom: 10px;"/>
            <input type="submit" value="Utiliser ce personnage" name="utiliser" style="margin-bottom: 10px;"/>
          </p>
      </form>
  </div>
</div>
<?php } ?>

</body>
</html>

<?php
if (isset($perso)) // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
{
  $_SESSION['perso'] = $perso;
}