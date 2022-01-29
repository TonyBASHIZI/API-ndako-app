<?php include_once 'config.php';
class Save{
	public function saveRetrait(){
		if (isset($_POST)) {
            $msg = array();
            $msg["error"]="";
            $msg["success"]="";
            $msg["soldeClient"]="";
            $msg["detailClient"]="";
            $msg["soldeAgent"]="";
            
            $matricule_client  = trim(htmlentities($_POST['matricule_client']));
            $montant_retrait =trim(htmlentities($_POST['montant_retrait'])) ;
            $clientPwd=trim(htmlentities($_POST['clientPwd'])) ;
            $agent_matricule=trim(htmlentities($_POST['agent_matricule'])) ;
            $typeDevise=trim(htmlentities($_POST['typeDevise'])) ;
            // $typeDevise="CDF";
            $solde=0;

            if ($matricule_client=="" || $montant_retrait=="" || $clientPwd=="" || $agent_matricule=="") 
            {
                $msg["error"] = 'Donnees invalides';
            } else 
            {
                $checkPWD=mysqli_query(Constants::connect(), "select * from detail_carte inner join t_client on t_client.id_carte=detail_carte.ref_nfc where ref_nfc='".$matricule_client."' and mot_de_passe='".$clientPwd."'");
                if (mysqli_num_rows($checkPWD) == 1) 
                {
                    $rowClient=mysqli_fetch_assoc($checkPWD);
                    $soldeClient=$rowClient["montant"];
                    $checkAgent=mysqli_query(Constants::connect(), "select * from devise where ref_matricule='".$agent_matricule."' and ref_taux='$typeDevise'");
                    if(mysqli_num_rows($checkAgent)==1)
                    {
                        $rowAgent=mysqli_fetch_assoc($checkAgent);
                        $soldeAgent=$rowAgent["solde"];
                        try
                        {
                            $commission=($montant_retrait*5)/100;
                            $soldeClient=$soldeClient-($montant_retrait+$commission);
                            $totalRetrait=$montant_retrait+$commission;
                            if($soldeClient<0)
                            {
                                $msg["error"]= "Le montant saisi est supérieur au montant du solde du compte client";
                            }
                            else
                            {
                                $soldeAgent=$soldeAgent+$montant_retrait;
                                // $totalBonusAgent=$montant_retrait+$totalBonus;
                                $querry1="insert into `transaction`(ref_type_transaction, ref_compte_client, ref_compte_agent, montant, ref_taux, commission) values ('RETRAIT', '".$matricule_client."', '".$agent_matricule."', '".$montant_retrait."', '".$typeDevise."', '".$commission."');";
                                $querry2="update devise  set solde=solde+'".$montant_retrait."' where ref_matricule='".$agent_matricule."' and ref_taux='".$typeDevise."';";
                                $querry4="update devise  set solde=solde+'".$commission."' where ref_matricule='MAG-CLIENT-0000' and ref_taux='".$typeDevise."';";
                                $querry3="update detail_carte  set montant=montant-'".$totalRetrait."' where ref_nfc='".$matricule_client."';";
                                // $res=mysqli_multi_query(Constants::connect(),$querry2.$querry3);
                                $res=mysqli_multi_query(Constants::connect(), $querry1.$querry2.$querry3.$querry4);
                                if($res)
                                {
                                    $msg["success"]= "success";
                                    $msg["soldeClient"]= $soldeClient;
                                    $msg["soldeAgent"]= $soldeAgent;
                                    $msg["detailClient"]='Matricule: '.$rowClient['matricule'].', Nom: '.$rowClient['nom'].' '.$rowClient['postnom'].' '.$rowClient['prenom'];
                                }
                                else
                                    $msg["error"]= "Server error while saving";
                            }
                        }
                        catch(Exception $e)
                        {
                            $msg["error"]="server error";
                            Constants::connect()->rollback();
                        }
                    }
                    else
                    {
                        $msg["error"]='Aucun compte agent trouvé avec le matricule la devise '.$typeDevise;
                    }
                }
                else
                {
                    $msg["error"]='Matricule ou mot de passe client incorrecte';
                }
            }
            
            mysqli_close(Constants::connect());
        }
        else
        {
            $msg["error"]='Data not received';
        }
        echo json_encode($msg);
	}

    public function saveClient(){
        if (isset($_POST)) {
            $msg = "";
            $nom  = trim(mysqli_real_escape_string(Constants::connect(),$_POST['nom']));
            $postnom =trim(mysqli_real_escape_string(Constants::connect(),$_POST['postnom'])) ;
            $prenom=trim(mysqli_real_escape_string(Constants::connect(),$_POST['prenom'])) ;
            $adresse=trim(mysqli_real_escape_string(Constants::connect(),$_POST['adresse'])) ;
            $telephone=trim(mysqli_real_escape_string(Constants::connect(),$_POST['telephone'])) ;
            $ref_IDcarte=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_carte'])) ;
            $sexe=trim(mysqli_real_escape_string(Constants::connect(),$_POST['sexe'])) ;
            $etatcivil=trim(mysqli_real_escape_string(Constants::connect(),$_POST['etatcivil'])) ;
            $age=trim(mysqli_real_escape_string(Constants::connect(),$_POST['age'])) ;
            $affiliation=trim(mysqli_real_escape_string(Constants::connect(),$_POST['affiliation'])) ;
            // $ref_IDcarte=trim(mysqli_real_escape_string(Constants::connect(),$_POST['ref_carte'])) ;
            $matricule="";
            $getLastID=mysqli_query(Constants::connect(), "select id from t_client order by id desc limit 1");
            $row=mysqli_fetch_assoc($getLastID);
            $lastID=$row['id'];
            $random=rand(0, 5000);
            $lastID=$lastID+1;
            $matricule=$random."-". date("m"). date("y")."-".$lastID."-".$random;
            if ($nom=="" || $postnom=="" || $prenom=="" || $telephone=="" || $ref_IDcarte=="") 
            {
                $msg = 'Donnees invalides';
            }
            else 
            {
                $checkCard="select * from detail_carte where ref_nfc='$ref_IDcarte';";
                $response=mysqli_query(Constants::connect(), $checkCard);
                if(mysqli_num_rows($response)>=1)
                {
                    $msg="Cette carte est déjà attribuée";
                }
                else
                {
                    try 
                    {
                        $querry5="INSERT INTO `t_client` (`matr_client`, `nom`, `postnom`, `prenom`, `adresse`, `reseaux`, `tel`, `refcat`, `id_carte`, sexe, etatcivil, affiliation) VALUES('$matricule','$nom', '$postnom', '$prenom', '$adresse', 'M00-M-4219-17', '$telephone', '01', '$ref_IDcarte', '$sexe', '$etatcivil', '$affiliation');";
                        $querry6="insert into `detail_carte`(ref_nfc, matricule, mot_de_passe, reseau) values('$ref_IDcarte', '$matricule', '1234', 'M00-M-4219-17');";
                        // $res=mysqli_query(Constants::connect(), $querry5);
                        $res=mysqli_multi_query(Constants::connect(), $querry5.$querry6);
                        if($res)
                        {
                            $msg= "success";
                            Constants::connect()->query("commit;");
                        }
                        else
                        {
                            var_dump(Constants::connect()->error);
                            Constants::connect()->query("rollback;");
                            $msg= "Server error";
                        }
                    } catch (Exception $e) {
                        var_dump($e);
                        $msg= "Server error";
                        Constants::connect()->query("rollback;");
                    }
                }
            }
            
            mysqli_close(Constants::connect());
        }
        else
        {
            $msg='Data not received';
        }
        echo json_encode($msg);
    }

    public function updateAgentPwd(){
        if(isset($_POST)){
            $msg="";
            $matricule=trim(mysqli_real_escape_string(Constants::connect(),$_POST['matricule']));
            $lastPwd=trim(mysqli_real_escape_string(Constants::connect(),$_POST['lastPwd']));
            $newPwd=trim(mysqli_real_escape_string(Constants::connect(),$_POST['newPwd']));
            if($lastPwd=="" || $newPwd==""){
                $msg="Donnees invalides";
            }
            else{
                $checkAccount="SELECT * FROM devise where ref_matricule='$matricule' and pwd='$lastPwd'";
                $response=mysqli_query(Constants::connect(), $checkAccount);
                if(mysqli_num_rows($response)==1){
                    $updateAccount="UPDATE devise set pwd='$newPwd' where ref_matricule='$matricule'";
                    $execQuery=mysqli_query(Constants::connect(), $updateAccount);
                    if($execQuery){
                        $msg="success";
                    }
                    else{
                        $msg="error occured";
                    }
                }else{
                    $msg="Ancien mot de passe incorrect";
                }
            }
        }else
        {
            $msg='Data not received';
        }
        echo json_encode($msg);
    }

    public function saveCard(){
        if(isset($_POST)){
            $msg="";
            $cardID=trim(mysqli_real_escape_string(Constants::connect(),$_POST['cardID']));
            if($cardID==""){
                $msg="Donnees invalides";
            }
            else{
                $checkAccount="SELECT * FROM carte where id_carte='$cardID'";
                $response=mysqli_query(Constants::connect(), $checkAccount);
                if(mysqli_num_rows($response)>=1){
                    $msg="Cette carte est deja enregistrée";
                }else{
                    $query="insert into `carte`(id_carte, status) values('$cardID', 'Active');";
                    $res=mysqli_query(Constants::connect(), $query);
                    if($res)
                        $msg= "success";
                    else
                        $msg= "Server error";
                }
            }
        }else
        {
            $msg='Data not received';
        }
        echo json_encode($msg);
    }
}
$saveInstance=new Save();
if(strtolower($_POST['transaction'])=="retrait"){
	$saveInstance->saveRetrait();
}
else if(strtolower($_POST['transaction'])=="client"){
	$saveInstance->saveClient();
}
else if(strtolower($_POST['transaction'])=="savecard"){
    $saveInstance->saveCard();
}
else if(strtolower($_POST['transaction'])=="updatepwd"){
	$saveInstance->updateAgentPwd();
}
?>