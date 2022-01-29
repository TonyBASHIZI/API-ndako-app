<?php
	$matricule="AG"."-".substr("KAMBALE", 0, 1) . "" . substr("LUSEKO", 0, 1)."". substr("JULIO", 0, 1)."-" . date("y-m-d");
	echo $matricule;
?>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="saveFunctions.php" method="post">
		<input placeholder="nom" type="text" name="nom">
		<input placeholder="post" type="text" name="postnom">
		<input placeholder="pren" type="text" name="prenom">
		<input placeholder="adresse" type="text" name="adresse">
		<input placeholder="telephone" type="text" name="telephone">
		<input placeholder="carte" type="text" name="ref_carte">
		<input placeholder="sexe" type="text" name="sexe">
		<input placeholder="etatcivil" type="text" name="etatcivil">
		<input placeholder="age" type="text" name="age">
		<input placeholder="affiliation" type="text" name="affiliation">
		<input placeholder="transaction" type="text" name="transaction" value="client">
		<input type="submit" value="valider">
	</form>

</body>
</html>