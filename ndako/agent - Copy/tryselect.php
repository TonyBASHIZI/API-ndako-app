<?php
        require_once "config2.php";
        $msg = array();
        // $ID=$_GET['ID'];
        try {
           // $fetch_user=mysqli_query($con, "select `numero`,`plaque`,`marque`,`place`, compagnie.noms as compagnieName from bus inner join compagnie on bus.ref_compagnie=compagnie.noms where plaque='$ID'");
            $username="mg-ag-028";
            $pwd="test";
           $fetch_user=mysqli_query(Constants::connect(), "SELECT * FROM devise where ref_matricule='$username' and pwd='$pwd'");

            if (mysqli_num_rows($fetch_user) >= 1) {
                while ($row = mysqli_fetch_assoc($fetch_user)) {
                    array_push($msg, $row);
                }
                // $msg="exists";
            } 
            else if (mysqli_num_rows($fetch_user) <= 0) 
            {
                $msg='Identifiant non trouvé';
            }
        } catch (Exception $e) {
           $msg="Erreur du serveur";
        }
        mysqli_close(Constants::connect());
        echo json_encode($msg);
?>
