<?php
        include "config.php";
        //Add the new data to the database.
        if (isset($_POST)) {
            $msg = array();
            $matricule_agent = trim(htmlentities($_POST['matricule_agent']));
            if ($matricule_agent=="") {
                $msg="Aucune données reçues";
            } else {
               try {
                   $fetch_user=mysqli_query($con, "select * from devise where ref_matricule='$matricule_agent'");

                    if (mysqli_num_rows($fetch_user) >= 1) {
                        while ($row = mysqli_fetch_assoc($fetch_user)) {
                            array_push($msg, $row);
                        }
                    } 
                    else if (mysqli_num_rows($fetch_user) <= 0) 
                    {
                        $msg='Aucun compte associe au matricule fourni';
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
