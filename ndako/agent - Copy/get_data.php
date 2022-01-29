<?php
include_once 'config2.php';
class SelectAll {

    public function select($query){
        if(Constants::connect() != null){
            $result = Constants::connect()->query($query);
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
    $username=$_POST['matricule'];
    $pwd=$_POST['pwd'];
    $sql = "SELECT * FROM devise where ref_matricule='$username' and pwd='$pwd'";
}
else if(strtolower($_POST['transaction'])=="checkbalance"){
  $cardID=trim(mysqli_real_escape_string(Constants::connect(),$_POST['cardID']));
  $sql = "SELECT * FROM detail_carte where ref_nfc='$cardID'";
}
$delivery = new SelectAll();
$delivery->select($sql);

?>