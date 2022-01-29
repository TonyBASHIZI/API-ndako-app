<?php
        include "config.php";
        if (isset($_POST)) {
            $msg = array();
            
            $id_carte = trim(htmlentities($_POST['idCarte']));
            if ($id_carte=="") {
                $msg="Aucune données reçues";
            } else 
            {
               try {
                   $fetch_user=mysqli_query($con, "select * from c1aldynam.detail_carte where ref_nfc='".$id_carte."'");

                    if (mysqli_num_rows($fetch_user) == 1) {
                        while ($row = mysqli_fetch_assoc($fetch_user)) {
                            array_push($msg, $row);
                        }
                    } 
                    else if (mysqli_num_rows($fetch_user) >1) 
                    {
                        $msg='Plusieurs indentifiants de la carte trouvés';
                    }
                    else
                    {
                        $msg='Carte non attribuée';
                    }
               } catch (Exception $e) {
                   $msg="Erreur du serveur";
               }
            }
            
            mysqli_close($con);
        }
        else
        {
            $msg="Aucune données reçues";
        }
            echo json_encode($msg);

?>
