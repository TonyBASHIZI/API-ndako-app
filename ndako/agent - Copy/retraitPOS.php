<?php
        include "config.php";
        if (isset($_POST)) {
            $msg = "";
            
            $matricule_client  = trim(htmlentities($_POST['matricule_client']));
            $montant_retrait =trim(htmlentities($_POST['montant_retrait'])) ;
            $clientPwd=trim(htmlentities($_POST['clientPwd'])) ;
            $agent_matricule=trim(htmlentities($_POST['agent_matricule'])) ;
            $typeDevise=trim(htmlentities($_POST['typeDevise'])) ;
            $typeDevise="CDF";
            $solde=0;

            if ($matricule_client=="" || $montant_retrait=="" || $clientPwd=="" || $agent_matricule=="") 
            {
                $msg = 'Donnees invalides';
            } else 
            {
                $checkPWD=mysqli_query($con, "select * from c1aldynam.detail_carte where matricule='".$matricule_client."' and mot_de_passe='".$clientPwd."'");
                if (mysqli_num_rows($checkPWD) == 1) 
                {
                    $rowClient=mysqli_fetch_assoc($checkPWD);
                    $soldeClient=$rowClient["montant"];
                    $checkAgent=mysqli_query($con, "select * from aloha_cash_db.devise where ref_matricule='".$agent_matricule."' and ref_taux='$typeDevise'");
                    if(mysqli_num_rows($checkAgent)==1)
                    {
                        try
                        {
                            $soldeClient=$soldeClient-($montant_retrait);
                            if($soldeClient<0)
                            {
                                $msg= "Le montant saisi est supérieur au montant du solde du compte client";
                            }
                            else
                            {
                                // $totalBonusAgent=$montant_retrait+$totalBonus;
                                $querry1="insert into aloha_cash_db.transaction(ref_type_transaction, ref_compte_client, ref_compte_agent, montant, ref_taux) values ('RETRAIT POS', '".$matricule_client."', '".$agent_matricule."', '".$montant_retrait."', '".$typeDevise."');";
                                $querry2="update aloha_cash_db.devise  set solde=solde+'".$montant_retrait."' where ref_matricule='".$agent_matricule."' and ref_taux='".$typeDevise."';";
                                $querry3="update c1aldynam.detail_carte  set montant=montant-'".$montant_retrait."' where matricule='".$matricule_client."';";
                                $res=mysqli_multi_query($con, $querry1.$querry2.$querry3);
                                if($res)
                                    $msg= "success";
                                else
                                    $msg= "Server error while saving";
                            }
                        }
                        catch(Exception $e)
                        {
                            $msg="server error";
                            $con->rollback();
                        }
                    }
                    else
                    {
                        $msg='Aucun compte agent trouvé avec le matricule la devise '.$typeDevise;
                    }
                }
                else
                {
                    $msg='Matricule ou mot de passe client incorrecte';
                }
            }
            
            mysqli_close($con);
        }
        else
        {
            $msg='Data not received';
        }

		echo json_encode($msg);
?>
