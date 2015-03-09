<?php
include_once 'mosHTML.php';
require_once 'google/appengine/api/mail/Message.php';
use google\appengine\api\mail\Message;

require_once 'google/appengine/api/users/UserService.php';
use google\appengine\api\users\UserService;

mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');

$nomor =  $_REQUEST['NOMOR'];

$user = UserService::getCurrentUser();

if ($user) {
 $emailpembuat = $user->getNickname(); 
} else {
 $emailpembuat =  $_REQUEST['EMAILBUAT'];
}

$nomor =  $_REQUEST['NOMOR'];

if ($nomor === '') {
	exit;
}

$sqlM = "SELECT M.NOMOR, M.NAMA, M.PERIHAL,  TIMESTAMPADD(HOUR,7,M.TANGGAL) as TANGGAL, M.MEMO, M.VALIDATOR, M.BAGIAN, M.NIK
				FROM MASTER_MEMO M
				WHERE 1=1 
				AND M.NOMOR = '$nomor' 
        ";

$resultM = mysql_query($sqlM);

if (!$resultM) {
    exit;
}

if (mysql_num_rows($resultM) == 0) {
    exit;
}

while ($rowM = mysql_fetch_assoc($resultM)) {
$nomormemo = $rowM["NOMOR"]; 
$SUBJECT = "INFO/MEMO/".$nomormemo; 
$message_body = "PEMBATALAN MEMO NO $nomormemo
"; 
$isimemo = 
" MEMO NOMOR : ". $rowM["NOMOR"]."

NAMA ". $rowM["NAMA"]."
NIK ". $rowM["NIK"]."
BAGIAN ". $rowM["BAGIAN"]."
TANGGAL ". $rowM["TANGGAL"]."
PERIHAL ". $rowM["PERIHAL"]."
MEMO : 

". $rowM["MEMO"];

$message_body = $message_body.$isimemo;
$emailvalidatorx = explode(',', $rowM["VALIDATOR"]);
}

$emailvalidatorx[] = $emailpembuat;

$mail_options = [   
 "sender" => "systemsm@siantarmaju.com", 
 "to" => $emailvalidatorx,   
 "subject" => $SUBJECT, 
 "textBody" => $message_body
 ];
 
 
 IF ($nomormemo !== '' ) {
 try {    
 
 $emaildikirim = new Message($mail_options);    
 $emaildikirim->send();
 echo "<br /> Selesai mengirim <br />"; ;

	$sqlB = "UPDATE MASTER_MEMO SET STATUS = 'BATAL'
				WHERE 1=1 
				AND NOMOR = '$nomor' 
        ";

	$resultB = mysql_query($sqlB);
	$nomor = '';
	$nomormemo = '';
	$emailvalidatorx = '';
	$message_body = '';
 } 
 catch (InvalidArgumentException $e) {
 echo $e;	
 }
 }

//*/	

?>
	<table>
	<tr><td><a href="lihatmemo.do">KEMBALI</a></td></tr>
	<?php $feedback = "mailto:systemsm@siantarmaju.com?subject=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>	
	</table>
<?PHP
 exit;
?>