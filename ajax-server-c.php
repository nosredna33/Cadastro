<?php
function generatePassword($length = 8) {
    $chars = '_#*$%abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = Rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

$publicfile_c = "data/pubkey_CPF";
$extFilePEM = ".pem";
$extFileJSON = ".json";
$cleartextfile = "data/cleartext";

/*
echo "CNES..: " . $_POST["action"];
echo "Action: " . $_POST["cnesID"];
*/
if($_POST["action"] == "pubkey") {
    $publickey_c = $_POST["publickey"];
	$cnesID_c = str_pad(preg_replace("/[^0-9]/", "", $_POST["cnesID"]),  7, '0', STR_PAD_LEFT);
	$cpfID_c =  str_pad(preg_replace("/[^0-9]/", "", $_POST["cpfID"]) , 11, '0', STR_PAD_LEFT);
	$cnsID_c =  str_pad(preg_replace("/[^0-9]/", "", $_POST["cnsID"]) , 15, '0', STR_PAD_LEFT);
	$senha_c =  generatePassword( 12 );
	openssl_public_encrypt($senha_c, $endata, $publickey_c);
	$xpwd = base64_encode($endata);
	$jsonOut = "{\n\t\"cpf\": "  . "\"" . $cpfID_c            . "\",\n" 
	         . "\t\"email\": "   . "\"" . $_POST["email"]     . "\",\n"
			 . "\t\"cnes\": "    . "\"" . $cnesID_c           . "\",\n"
			 . "\t\"cnes\": "    . "\"" . $cnsID_c            . "\",\n"
			 . "\t\"pwd\": "     . "\"" . $senha_c            . "\",\n"
			 . "\t\"xpwd\": "    . "\"" . $xpwd               . "\",\n"
			 . "\t\"pubkey\": "  . "\"" . preg_replace("/[\n\r]/", "", $_POST["publickey"]) . "\"\n}";
			
	$fNamePubKey = $publicfile_c . $cpfID_c . $extFilePEM ;
	$fNameJson =   $publicfile_c . $cpfID_c . $extFileJSON ;
    file_put_contents($fNamePubKey, $publickey_c);
    file_put_contents($fNameJson, $jsonOut);
    echo "A sua chave pública foi devidamente salva no servidor...";
}

if($_POST["action"] == "get") {
	$cpfID_c =  str_pad(preg_replace("/[^0-9]/", "", $_POST["cpfID"]) , 11, '0', STR_PAD_LEFT);
	$fNamePubKey = $publicfile_c . $cpfID_c . $extFilePEM ;
	$fNameJson =   $publicfile_c . $cpfID_c . $extFileJSON ;
	$publickey_c = file_get_contents($fNamePubKey);
	$string = file_get_contents($fNameJson);
    $json_a = json_decode($string, true);

    // echo $json_a['John'][status];
	// $cleartext = file_get_contents($cleartextfile);
	// openssl_public_encrypt($cleartext, $endata, $publickey_c);
    echo $json_a['xpwd']; 	
}

if($_POST["action"] == "print") {
	$cpfID_c =  str_pad(preg_replace("/[^0-9]/", "", $_POST["cpfID"]) , 11, '0', STR_PAD_LEFT);
	$fNamePubKey = $publicfile_c . $cpfID_c . $extFilePEM ;
	// $fNameJson =   $publicfile_c . $cpfID_c . $extFileJSON ;
	if ( file_exists($fNamePubKey) ) {
		$string = file_get_contents("imprimir.html");
		echo $string;
	} else {
		echo "Você ainda não está cdastrado!";
	}
}

?>