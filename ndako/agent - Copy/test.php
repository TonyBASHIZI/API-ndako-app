<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <form action="saveFunctions.php" method="post">
        <input type="text" name="matricule_client" placeholder="client id">
        <input type="text" name="clientPwd" placeholder="client pwd">
        <input type="text" name="agent_matricule" placeholder="agent id">
        <input type="text" name="montant_retrait" placeholder="montant">
        <input type="text" name="typeDevise" placeholder="devise">
        <input type="text" name="transaction" value="retrait">
        <input type="submit" name="submit" value="save">
    </form>

</body>
</html>