<?php
include_once 'config.php';
class SelectAll {

    public function select($query){
        if(Constants::connect() != null){
            $result = Constants::connect()->query($query);
            // var_dump($result);
            if ($result->num_rows > 0) {
                $array = array();
                while ($row=$result->fetch_assoc()) {
                   array_push($array, $row);
                }
                print(json_encode($array));
            } else {
                print(json_encode("No data found"));
            }  
        }else {
            print(json_encode("Cannot connect to Database Server"));
        }
    }
}
$sql = "";
if ($_POST['transaction'] == 'login') {
    $username=$_POST['username'];
    $pwd=$_POST['pwd'];
    $sql = "SELECT * FROM proprietaire where email='$username' and tel='$pwd'";
}
else if(strtolower($_POST['transaction'])=="getbatiment"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT bat.id, bat.designation, bat.adresse, bat.type_bat, bat.statut, bat.photo, bat.description, concat(pro.nom,' ', pro.postnom) proprio, pro.tel from batiment bat inner join proprietaire pro on pro.tel=bat.ref_proprietaire where pro.tel='$ref_proprio'";
}
else if(strtolower($_POST['transaction'])=="getporte"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT porte.id, porte.designation, porte.prix_mois, porte.niveau_bat, porte.photo, porte.statut, bat.designation nom_batiment, bat.adresse  from porte inner join batiment bat on bat.id=porte.ref_bat inner join proprietaire pro on pro.tel=bat.ref_proprietaire where pro.tel='$ref_proprio'";
}
else if(strtolower($_POST['transaction'])=="getgarantie"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT g.id, g.designation designGarantie, g.nbmois, g.montant, g.date_debut, g.date_fin, g.etat, (p.prix_mois*nbmois) totalAPayer, ((p.prix_mois*nbmois)-montant) reste, p.designation, p.prix_mois, p.niveau_bat from garatie g inner join porte p on p.id=g.ref_porte inner join batiment bat on bat.id=p.ref_bat inner join proprietaire pro on pro.tel=bat.ref_proprietaire where pro.tel='$ref_proprio'";
}
else if(strtolower($_POST['transaction'])=="getpaiement"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT mouv.id, mouv.created_at dateMouv, mouv.nbmois, mouv.type_mvt, mouv.montant mntPaiement, po.designation designPorte, po.niveau_bat, po.prix_mois prixPorte, po.statut statutPorte, bat.designation designBat, users.tel telGerant,sum(mouv.montant) totalPayement from mouvement mouv inner join porte po on po.id=mouv.ref_porte inner join batiment bat on bat.id=po.ref_bat inner join proprietaire pro on pro.tel=bat.ref_proprietaire inner join utilisateur users on users.tel=mouv.gerant where pro.tel='$ref_proprio'";
}
else if(strtolower($_POST['transaction'])=="getdepense"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT dep.id, dep.libelle, dep.created_at dateMouv, dep.montant mntPaiement, po.designation designPorte, 
          po.niveau_bat, po.prix_mois prixPorte, po.statut statutPorte, bat.designation designBat, users.tel telGerant, sum(dep.montant) totalDepense 
          from depense dep 
          inner join porte po on po.id=dep.ref_porte 
          inner join batiment bat on bat.id=po.ref_bat 
          inner join proprietaire pro on pro.tel=bat.ref_proprietaire
          inner join utilisateur users on users.tel=dep.gerant where pro.tel='$ref_proprio'";
}
else if(strtolower($_POST['transaction'])=="getstat"){
  $ref_proprio=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_proprio']));
  $sql = "SELECT id,nom, postnom, tel, email,(select count(*) from porte,batiment bat, proprietaire pro where bat.id=porte.ref_bat and pro.tel=bat.ref_proprietaire and pro.tel='$ref_proprio' and porte.statut='disponnible') countPorteDispo, (select count(*) from porte,batiment bat, proprietaire pro where bat.id=porte.ref_bat and pro.tel=bat.ref_proprietaire and pro.tel='$ref_proprio' and porte.statut like '%occup%') countPorteOccupe, (select count(*) from porte,batiment bat, proprietaire pro where bat.id=porte.ref_bat and pro.tel=bat.ref_proprietaire and pro.tel='$ref_proprio') countPorteAll from proprietaire where proprietaire.tel='$ref_proprio'";
}
$delivery = new SelectAll();
$delivery->select($sql);

?>