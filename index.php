<?php
 
function my_autoloader($class) {
    include  $class.'.php';
}
 
spl_autoload_register('my_autoloader');
 
session_start();
 
if (isset($_GET['deconnexion'])){
    session_destroy();
    header('Location: .');
    exit(); 
}
  
if (isset($_SESSION['Etud'])){
    $Etud = $_SESSION['Etud'];
}
 
$db = new PDO('mysql:host=localhost;dbname=combat','root','');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
 
$Etudiants = new EtudiantBD($db);
  
if (isset($_POST['creer']) && isset($_POST['nom'])){
    $Etud = new Etudiants(['nom' => $_POST['nom']]);
     
    if (!$Etud->nomValide()){
        $message = 'Le nom choisi est invalide.';
        unset($Etud);
    } elseif ($Etudiants->exists($Etud->nom())){
        $message = 'Le nom du Etudiant est déjà pris.';
        unset($Etud);
    } else {
        $Etudiants->add($Etud);
    }
     
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])){
    if ($Etudiants->exists($_POST['nom']))
    {
        $Etud = $Etudiants->get($_POST['nom']);
         
        
            if ($Etud->degats() >= 10) {
                $Etud->setDegats($Etud->degats() - 10);
            }
            $Etudiants->update($Etud);
        
         
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
     
} elseif (isset($_GET['frapper'])){
     
    if (!isset($Etud)){
        $message = 'Merci de créer un Etudiant ou de vous identifier.'; 
    } else {
        if (!$Etudiants->exists((int) $_GET['frapper'])){
            $message = 'Le Etudiant que vous voulez frapper n\'existe pas!';
        } else {
             
            $EtudiantAFrapper = $Etudiants->get((int) $_GET['frapper']);
            $retour = $Etud->frapper($EtudiantAFrapper);   
             
            switch($retour)
            {
                case Etudiants::CEST_MOI :
                    $message = 'Mais... pouquoi voulez-vous vous frapper ???';
                    break;
					
                case Etudiants::ETUDIANT_FRAPPE :
                    $message = 'Le Etudiant a bien été frappé !';
                     
                    $Etud->gagnerExperience();
                     
                    $Etudiants->update($Etud);
                    $Etudiants->update($EtudiantAFrapper);
                     
                    break;
                case Etudiants::ETUDIANT_TUE;
                    $message = 'Vous avez tué ce Etudiant !';
                     
                    $Etud->gagnerExperience();
 
                    $Etudiants->update($Etud);
                    $Etudiants->delete($EtudiantAFrapper);
                     
                    break;
            }
        }
    }
}
 
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" media="screen" type="text/css" title="Design" href="theme.css" />
<h1>   <marquee> projet php fromwork </marquee> </h1>
<div id="box">
<h2><u>Nombre de Etudiant créés : <?= $Etudiants->count() ?></u></h2>

</head>
    <body>
      <table width="349" height="215" align="center">
	<h3><center> <strong>-----  Ajouter ici votre information  -----</strong>
		<tr><td><br></td></tr>       
    <?php
        if (isset($message)){
            echo '<p>'. $message . '</p>';
        }
         
        if (isset($Etud)){
        ?>
            <p><strong><a href="?deconnexion=1">Déconnexion</a></strong></p>
         
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nom : <?=  htmlspecialchars($Etud->nom()) ?><br />
                    Dégâts : <?= $Etud->degats() ?>
                    Expérience : <?= $Etud->experience() ?>
                    Nombre des Frappe : <?= $Etud->nbfrap() ?>
                   
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui frapper?</legend>
                <p>
                    <?php
                     
                    $Etuds= $Etudiants->getList($Etud->nom());  
                    if (empty($Etuds)) {
                        echo 'Etudiant à frapper!';
                    } else 
					{
                        foreach($Etuds as $unEtudiant){
                            echo '<a href="?frapper='.$unEtudiant->id().'">'.htmlspecialchars($unEtudiant->nom()).'</a> dégâts : '.$unEtudiant->degats().',<br> expérience : '.$unEtudiant->experience().', nombre des frap : '.$unEtudiant->nbfrap().'<br />';
                             
                        }
                    }
                     
                    ?>
                </p>
            </fieldset>
             
         
        <?php
 
        } else {
             
    ?>
            <form action="" method = "post">

					<td >
                    Nom : <input type="text" name="nom" maxlength="50" />
</td>
</tr> 
<tr><td><br></td></tr>
<tr>
<td align="center" colspan="2">
<input type="submit" value = "Utiliser Etudiant" name="utiliser" /></td>

<td align="center" colspan="2">
                    <input type="submit" value = "Créer ce Etudiant" name="creer" /></td>
</tr>

</table>
</form>
</div id="box">
                 
    <?php
        }
    ?>
     
     
     
    </body>
</html>
<?php
if (isset($Etud)){
    $_SESSION['Etud'] = $Etud;
}
?> 