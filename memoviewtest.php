<?php
require_once 'google/appengine/api/mail/Message.php';
use google\appengine\api\mail\Message;

mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');

//print_r($_REQUEST);

$nomor = isset($_REQUEST['nomornya'])? $_REQUEST['nomornya'] : ''; 	

$MANUALVALIDATIONCODE = isset($_REQUEST['tbox'])? $_REQUEST['tbox'] : ''; 	
$MANUALVALIDATIONAO = isset($_REQUEST['tbox1'])? $_REQUEST['tbox1'] : ''; 	
$MANUALVALIDATIONEMAIL = isset($_REQUEST['tbox2'])? $_REQUEST['tbox2'] : ''; 	

$jml = COUNT($MANUALVALIDATIONAO);

for ($i=0;$i<$jml;$i++) {
$subjectmemo = 'SYS:APPROVAL/SM/MEMO/'.$nomor	;

IF ( !empty($MANUALVALIDATIONCODE[$i])) {
//echo "<br />". $MANUALVALIDATIONAO[$i] . "  " .$MANUALVALIDATIONCODE[$i] . '<br />' ;
$emailao = $MANUALVALIDATIONEMAIL[$i];
$kodeao = $MANUALVALIDATIONCODE[$i];
$sql =  "SELECT 'Y' as replynya
FROM DETAIL_MEMO
WHERE 1=1
AND NOMOR = '$nomor' 
AND SUBSTRING(KODE,1,6) = '$kodeao'
AND VALIDATOR = '$emailao'
UNION 
SELECT 'T'  as replynya 
FROM DETAIL_MEMO
WHERE 1=1
AND NOMOR = '$nomor' 
AND SUBSTRING(KODE,7,6) = '$kodeao'
AND VALIDATOR = '$emailao'
";
$cekresult = mysql_query($sql);

while ($row = mysql_fetch_assoc($cekresult)) {
	$replytype = $row["replynya"]; 
}

if (!empty($replytype)) { 

$isiemail = "Dari:". $MANUALVALIDATIONEMAIL[$i] . "
 " .$replytype.":".$MANUALVALIDATIONCODE[$i] . "
"." # #
";

$sql = "INSERT INTO TERIMAEMAIL (SUBYEK, DARI, CC, ISIEMAIL, PROSES) VALUES ('$subjectmemo' , 'user@siantarmaju.com' , '', '$isiemail' , 'F')";
$addmemo = mysql_query($sql);

//date_default_timezone_set('Asia/Bangkok');

// mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
// mysql_select_db('SIMIS');

syslog(LOG_INFO, "mulai approval by manual code");

$mailadmin = false;
$penolakan = false;
$confirm = false;
$approval = false;
$tidakmenjawab = false;

$sql = "DELETE FROM MASTER_MEMO WHERE NAMA IS NULL";        
$resultSQL = mysql_query($sql);//or die(mysql_error());

$sql = "SELECT NOMOR, MASTER_ID FROM MASTER_MEMO WHERE 1=1 AND STATUS IS NULL";        
$resultSQL = mysql_query($sql);//or die(mysql_error());

//$sql = "DELETE FROM TERIMAEMAIL WHERE ISIEMAIL = 'Dari:'";        
//$resultSQL = mysql_query($sql);//or die(mysql_error());
	
while ( false !== $row2 = mysql_fetch_assoc($resultSQL) )
{				
	$nomor = $row2["NOMOR"];
	$adoquery = " select SUBSTRING( isiemail, locate('<',isiemail)+1, locate('>',isiemail) - (locate('<',isiemail)+1)) as dari, SUBSTRING( isiemail, locate('Dari:',isiemail)+5, locate('.com',isiemail)+5 - (locate('Dari:',isiemail)+4)) as dari2, isiemail, subyek, cc, proses FROM TERIMAEMAIL WHERE SUBYEK LIKE '%".$nomor."%' AND proses = 'F'";
	$adoresultSQL = mysql_query($adoquery);// or die(mysql_error());
	if ($nomor == '') {
		//exit;
		continue;
	}

	while ( false !== $row3 = mysql_fetch_assoc($adoresultSQL) ) 
	{ 
	 
	//foreach ($row3 as $value){
		//$email = $value;
		$dari = $row3["dari"];//$email[0];
		$posdari = strpos($dari,'@siantarmaju' );
		if ($posdari === false ) {
			$dari = trim($row3["dari2"]);
		}
		
		syslog(LOG_INFO, ' from = '.$dari );
		
		$isiemail = $row3["isiemail"];//$email[1];
		$subyek = $row3["subyek"];//$email[2];
		$cc = $row3["cc"];//$email[3];
		$confirm = $row3["proses"];//$email[4];

		$arrsubject3 = explode('APPROVAL' , $subyek);				
		
		$isisubjectasli = 'INFO'. $arrsubject3[1];
		$subjectasli = $subyek;

		$status = $dari;
		
		$qusersm = "select user_name, alamatemail from MASTER_USER_ACCOUNT where ((alamatemail like trim('%$dari%')) or (nama like trim('%$dari%')))"; 
		$xusersm = mysql_query($qusersm);
		$xrowusersm = mysql_fetch_assoc($xusersm);
		$status = $xrowusersm['user_name'];
		$validator = $xrowusersm['alamatemail'];

		$mailbody =  $isiemail;
		$posYa = strpos($isiemail,"Y:"); 
		$posTidak = strpos($isiemail,"T:");				
		$abc = explode('Y:',$isiemail );	
		$abc2 = explode('T:',$isiemail);				

		
		if  ($posYa == 0 && $posTidak == 0) {
			$posYa = strpos($isiemail,"Y:");
			$abc = explode('Y:',$isiemail );						
			$posTidak = strpos($isiemail,"T:"); 
			$abc2 = explode('T:',$isiemail);
		} // end of if  ($posYa == 0 && $posTidak == 0) line 59   	 					
		
		$header_value = $subjectasli;

		  //IF (eregi( "APPROVAL/", $subjectasli) && eregi( "/MEMO/", $subjectasli) ){
			IF (preg_match( '#APPROVAL/#i', $subjectasli) && preg_match( '#/MEMO/#i', $subjectasli) ){
			$arrHeader = explode('APPROVAL/',$subjectasli);
			$frm  = explode('/MEMO/',$subjectasli);
			$approval = true;

//				} ELSE IF (eregi( "APPROVAL/", $subjectasli) && eregi( "MIS", $subjectasli) ) {
//					$frm  =explode('APPROVAL/',$subjectasli);
//					$approval = false;
			
		} ELSE {					
			$frm = explode('VALIDASI/',$subjectasli);
			$approval = false;
		}							
		//IF (eregi( "INFO/", $subjectasli)) {
		IF (preg_match( '#INFO/#i', $subjectasli)) {
			$approval = false;
			$tidakmenjawab = true;			 				
		} 				
		
		$jenis = 'MEMO';
		$nomornya = $nomor;
		$no = $nomor;

		$jawabya = count($abc);
		$jawabtdk = count($abc2);

		if ($jawabya == $jawabtdk ) {
		 $tidakmenjawab = true;
		 
			$posYa = strpos($isiemail,"Y:"); 
			$posTidak = strpos($isiemail,"T:");
			if  ($posYa == 0) {
			$posYa = strpos($isiemail,"Y:"); 
			$posTidak = strpos($isiemail,"T:");
			}

			 $posLimit = strpos($isiemail,"From:");	
			 if ($posLimit === 0 ) {
					$posLimit = strpos($isiemail,"$status");				
					if ($posLimit === 0 ) {
						$posLimit = strpos($isiemail,"APPROVAL/");				
					}
			 }

			 if ( $posLimit < $posYa && $posLimit < $posTidak ){
				$tidakmenjawab = true;
			 } else if ( $posLimit > $posYa) {
				$keterangan = trim(substr($abc[1],0,6));
				$mailadmin = true;
				$tidakmenjawab = false;
			 } else if ( $posLimit > $posTidak) {
				$keterangan = trim(substr($abc2[1],0,6) );
				$penolakan = true;   
				$tidakmenjawab = false;
			} else {
				$tidakmenjawab = true;
			}
		} else if($jawabya > $jawabtdk) {
			 $keterangan = trim(substr($abc[1],0,6));
			 $mailadmin = true; 
		} else if($jawabya < $jawabtdk) {
			$keterangan = trim(substr($abc2[1],0,6) );
						$penolakan = true;
		}
		//IF (eregi( "INFO/", $subjectasli)) {
		IF (preg_match( '#INFO/#i', $subjectasli)) {
			$approval = false;
			$tidakmenjawab = true;			 				
		} 

		if (substr($nomor,0,3) !== 'MIS' && strlen($nomor) == 11 && $tidakmenjawab == false) {

			$xsql = " select count(detail_id) as belumdivalidasi 
			from DETAIL_MEMO
			where nomor = '$nomor'
			"; 
			$xresult2 = mysql_query($xsql);
			$xrow2 = mysql_fetch_assoc($xresult2);
			$xtotalapproval = $xrow2['belumdivalidasi'];
      $xrow2 = '';
			$xsqljawabya = "select count(detail_id) as valid ,master_id
			from DETAIL_MEMO
			where nomor = '$nomor' AND trim(replyemail) = substr(kode,1,6) 
			group by master_id"; 
			$xresultvalid = mysql_query($xsqljawabya);
			$xrowvalid = mysql_fetch_assoc($xresultvalid);
			$xnumberofvalid = $xrowvalid['valid'];
			$xrowvalid='';
			$xsqljawabtdk = "select count(detail_id) as tolak ,master_id
			from DETAIL_MEMO
			where nomor = '$nomor' AND trim(replyemail) = substr(kode,7,6) 
			group by master_id"; 
			$xresultjwbtdk = mysql_query($xsqljawabtdk);
			$xrowjwbtdk = mysql_fetch_assoc($xresultjwbtdk);
			$xnumbernotvalid = $xrowjwbtdk['tolak'];          
			$xrowjwbtdk='';
			$kirimmemo = true;
			
			$statusapproval = ($xnumberofvalid + $xnumbernotvalid) - $xtotalapproval;				
			
			if ( $statusapproval == 0 ) {
//				$kirimmemo = false;
			}		

			$arrkonfirmasi = array();
			$arrkonfirmasi = explode('#',$mailbody);
			if (isset($arrkonfirmasi[1])) {
			$isikonfirmasi = (TRIM($arrkonfirmasi[1]) === ' ')? 'Tidak Ada' : TRIM($arrkonfirmasi[1]);
			} else {
			$isikonfirmasi = 'Tidak Ada';
			}

			$sqlstatusmemo = "select count(master_id) as nilai  from MASTER_MEMO
			where nomor = '$nomor' AND status is null "; 
			$xresultstatusmemo = mysql_query($sqlstatusmemo);
			$xrowstatusmemo = mysql_fetch_assoc($xresultstatusmemo);
			$bisaoveride = $xrowstatusmemo['nilai'];  
			if (!$keterangan == '') {  // $keterangan = jawaban di line 116, 120, 127, 130

				if  ($bisaoveride > 0 ) {   
					$sql = "UPDATE DETAIL_MEMO 
					SET REPLYEMAIL = '$keterangan' , KETERANGAN = '$isikonfirmasi' 
					WHERE NOMOR = '$nomor' AND VALIDATOR = '$validator'AND ( substr(kode,7,6) = '$keterangan' OR substr(kode,1,6) = '$keterangan' )
					";		
					mysql_query($sql);// or die(mysql_error());				
					$sql = "UPDATE TERIMAEMAIL set proses = 'T' WHERE subyek like '%$subyek%' and isiemail like '%$isiemail%'";
					mysql_query($sql);// or die(mysql_error());				
				} 

				$sql = "select 'disetujui' as jawaban from DETAIL_MEMO
				where 1=1
				and validator = '$validator'
				and NOMOR = '$nomornya'
				and SUBSTRING(kode,1,6) = REPLYEMAIL
				union all 
				select 'ditolak' as jawaban from DETAIL_MEMO
				where 1=1
				and validator = '$validator'
				and NOMOR = '$nomornya'
				and SUBSTRING(kode,7,6) = REPLYEMAIL
				";		
				
				$notifresult = mysql_query($sql);
				$NotifJawaban = mysql_fetch_assoc($notifresult);
				$keputusan = $NotifJawaban['jawaban'];
				
				$sql = "SELECT MASTER_USER_ACCOUNT.NIK as NIK, upper(`MASTER_USER_ACCOUNT`.nama) as NAMA
					from MASTER_USER_ACCOUNT 
					where MASTER_USER_ACCOUNT.USER_NAME = '$status' 
				";		

				$notifuserresult = mysql_query($sql) ; //or die(mysql_error());						 
				$Notifuser = mysql_fetch_assoc($notifuserresult);												 						

				$namajawaban = $Notifuser['NAMA'];
				$nikjawaban = $Notifuser['NIK'];

				IF ( ($keputusan === 'disetujui')||($keputusan === 'ditolak') ) {
					$isiemailx = "dengan memasukan kode yang diberikan oleh ".$namajawaban." (".$nikjawaban.") kepada pembuat memo. Memo ini telah ".$keputusan." oleh ".$namajawaban." (".$nikjawaban.") " ; 
					$isiemailx .= $isiemail;
					$validator = strtolower($validator);
						
					$message_body =  $isiemailx;

					$mail_options = [   
					 "sender" => "systemsm@siantarmaju.com", 
					 "to" => $validator,   
					 "subject" => $isisubjectasli, 
					 "textBody" => $message_body
					 ];
					 

					 try {    
						$emaildikirim = new Message($mail_options);    
						$emaildikirim->send();
						echo "Selesai mengirim ".$validator ;
						} 
						catch (InvalidArgumentException $e) {
						echo $e;	
						}
				} // end of IF ( ($keputusan === 'disetujui')||($keputusan === 'ditolak') )  line 224							
			} // END OF if (!$keterangan == '') LINE 177 	

			$sql = " select count(detail_id) as belumdivalidasi 
			from DETAIL_MEMO
			where replyemail is null AND nomor = '$nomornya'
			"; 
			$result2 = mysql_query($sql);// or die(mysql_error());
			$row2 = mysql_fetch_assoc($result2);
			$totalapproval = $row2['belumdivalidasi'];
      $row2 = '';			
			$sqljawabya = "select count(detail_id) as valid ,master_id
			from DETAIL_MEMO
			where nomor = '$nomornya' AND trim(replyemail) = substr(kode,1,6) 
			group by master_id"; 
			$resultvalid = mysql_query($sqljawabya);// or die(mysql_error());
			$rowvalid = mysql_fetch_assoc($resultvalid);
			$numberofvalid = $rowvalid['valid'];
			$master_id = $rowvalid['master_id'];					
			$rowvalid = '';
			$sqljawabtdk = "select count(detail_id) as tolak ,master_id
			from DETAIL_MEMO
			where nomor = '$nomornya' AND trim(replyemail) = substr(kode,7,6) 
			group by master_id"; 
			$resultjwbtdk = mysql_query($sqljawabtdk);// or die(mysql_error());
			$rowjwbtdk = mysql_fetch_assoc($resultjwbtdk);
			$numbernotvalid = $rowjwbtdk['tolak'];          

			$sql3 = " select count(detail_id) as needvalidation 
			from DETAIL_MEMO
			where nomor = '$nomornya' "; 
			$result = mysql_query($sql3);// or die(mysql_error());
			$row32 = mysql_fetch_assoc($result);
			$numberofall = $row32['needvalidation'];		
			$sqlmail = " select * from MASTER_MEMO
			where nomor = '$nomornya'"; 
			$resultx = mysql_query($sqlmail);// or die(mysql_error());
			$rowx = mysql_fetch_assoc($resultx);
			$waktu = date('d-M-Y H:i:s');						
			$to =$rowx['TO'];
			$validator =$rowx['VALIDATOR'];
			$backsend = $rowx['EMAIL'];						

			$memo =$rowx['MEMO'];
			$binmemo =$rowx['KODE'];
			$nama =$rowx['NAMA'];
			$nik = $rowx['NIK'];
			$cc =  $rowx['KETERANGAN'];
			$master_id = $rowx['MASTER_ID'];
			$masterstatus = $rowx['STATUS'];						
			$perihal = $rowx['PERIHAL'];						
			$validators = array();
			$tos = array();
			$ccs = array();

			if ($totalapproval == 0 && $numbernotvalid == 0 && $tidakmenjawab == false) {
//				$master_id = $rowvalid['master_id'];					
				$validators = explode(",",$validator ); 
				$arrvalidator =  '("'.implode('", "', $validators).'")';
				$queryval = "select nama, user_name, alamatemail FROM MASTER_USER_ACCOUNT where trim(alamatemail) in $arrvalidator and aktif='Y'" ;				
				$databaseval = mysql_query($queryval);// or die(mysql_error()); //->setQuery($queryval);						
				$arrval = array();	
				$arrkon = array();
				$arrvaltxt = array();	
				$arrkontxt = array();
				while ( false !== $rowval = mysql_fetch_array($databaseval) ){
						$sqlkon = " select keterangan from DETAIL_MEMO where 
							trim(validator) = '".$rowval['alamatemail']."'    and nomor = '$nomornya' "; 
						$resultkon = mysql_query($sqlkon);// or die(mysql_error());
						$rowkon = mysql_fetch_assoc($resultkon);              
						$arrval[] = ($rowval['nama']) ."\n".$rowkon['keterangan']."\n";
						$arrkon[] = ($rowval['nama'])." : ".$rowkon['keterangan']."\n";
						$arrvaltxt[] = ($rowval['nama']) ."
	".$rowkon['keterangan']."
	";
						$arrkontxt[] = ($rowval['nama'])." : ".$rowkon['keterangan']."
	";
				} //end of while ( false !== $rowval = mysql_fetch_array($databaseval) ) line 312

				$validatby =implode("",$arrval);
				$konvalidasi =implode("",$arrkon);

				$validatbytxt =implode("",$arrvaltxt);
				$konvalidasitxt =implode("",$arrkontxt);
				
				$tos = explode(",",$to);				
				$tox = '("'.implode('", "', $tos).'")';

				$queryto = " select nama FROM MASTER_USER_ACCOUNT where trim(alamatemail) in $tox and aktif = 'Y'" ;				

				$databaseto = mysql_query($queryto);// or die(mysql_error()); //->setQuery($queryval);
				$arrto = array();	
				$arrtotxt = array();
				while ( false !== $rowto = mysql_fetch_array($databaseto) ){				
					$arrto[] = ($rowto['nama']) ."\n";
					$arrtotxt[] = ($rowto['nama']) ."
	";							
				} // end of while ( false !== $rowto = mysql_fetch_array($databaseto) ) line 338
					$nameto =implode("",$arrto);
					$nametotxt =implode("",$arrtotxt);

				if (empty($cc)) {
					$namecc = ''; 
					 $namecctxt = ''; 
				} else { 
					$ccs = explode(",",$cc ); 
					$Qcc =  '("'.implode('", "', $ccs).'")';
					$querycc = "select nama FROM MASTER_USER_ACCOUNT where trim(alamatemail) in $Qcc and aktif = 'Y'" ;

					$databasecc = mysql_query($querycc);// or die(mysql_error()); 
					$arrcc = array();	
					$arrcctxt = array();	
					while ( false !== $rowcc = mysql_fetch_array($databasecc) ){ 
						$arrcc[] = ($rowcc['nama']) ."\n"; 
						$arrcctxt[] = ($rowcc['nama']) ."
	";
					}
					$namecc =implode("",$arrcc);
					$namecctxt =implode("",$arrcctxt);
				} // end of else line 347

				if ($numberofall == $numberofvalid && $numberofvalid > 0){
					$mailsubject = 'DOC/SM/MEMO/'.$nomornya;
					$sqlvalz = "SELECT validator from MASTER_MEMO where NOMOR = '$nomornya' ";		
					$validatorresult = mysql_query($sqlvalz) ; //or die(mysql_error());						 
					$validatorresults = mysql_fetch_assoc($validatorresult);												 						
					$xvalid = $validatorresults['validator'];			
					if ($cc !== '') 
						{ 
							$cc .= ','.$xvalid.','.$backsend; 							
//							$headers .= "Cc: ".$cc."\r\n"; 
//						} else {
//							$headers .= "Cc: ".$xvalid."\r\n"; 
						}
					$ccs = explode(",",$cc );	
					$txtmessage = '
MEMO NOMOR :'.$nomornya.'
PERIHAL :'.$perihal.'

DIBUAT OLEH
NAMA/NIK :'.$nama.'/'.$nik.'
TANGGAL JAM:'.$waktu.'

KEPADA:'.$nametotxt.'

MEMO : '."
$binmemo".'

MENYETUJUI:'."
$validatbytxt".'
';

//TEMBUSAN :'."
//$namecctxt".'
///* duedate
	$query = "select duedatereminder
	from REMINDER_MEMO
	where 1=1
	AND MASTER_ID = '$nomornya'
	" ;
	syslog(LOG_INFO, $query);	
	$hasiladdmemo = mysql_query($query);	
 	while ($addmemoresult = mysql_fetch_assoc($hasiladdmemo) ) {
		$remindermemo = $addmemoresult['duedatereminder'];
	}
	IF (!empty($remindermemo)) {
	$isiremindermemolong ='https://mail.google.com/mail/u/0/?shva=1#search/DOC+MEMO+'.$nomornya;
	$article1 = new stdClass();
	$article1->longUrl = $isiremindermemolong;
	$json_data1 = json_encode($article1);
	$post1 = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC_dNVDslL-yksuVeduN7v7w7ElSEk-21U',null,stream_context_create(array(
		'http' => array(
			'protocol_version' => 1.1,
			'user_agent'       => 'Ariska',
			'method'           => 'POST',
			'header'           => "Content-type: application/json\r\n".
								  "Connection: close\r\n" .
								  "Content-length: " . strlen($json_data1) . "\r\n",
			'content'          => $json_data1,
		),
	)));
	 
	$json1 = json_decode($post1, TRUE);
	$isiremindermemo=$json1['id'];

	$th = substr($remindermemo,0,4);
	$bln = substr($remindermemo,4,2);
	$hari = substr($remindermemo,6,2);
	$tanggalend = date('Ymd', mktime(0,0,0,$bln,$hari+1,$th));
	
	$linkperihal = str_replace(' ','+',$perihal);	

	$linkreminderlong = 'https://www.google.com/calendar/render?action=TEMPLATE&text=Reminder+memo:'.$linkperihal
	.'&dates='.$remindermemo.'/'.$tanggalend
	.'&details='.$isiremindermemo
	.'&sf=true&output=xml'
	;

	$article = new stdClass();
	$article->title = "An example article";
	$article->longUrl = $linkreminderlong;	 
	$json_data = json_encode($article);	 
	$post = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC_dNVDslL-yksuVeduN7v7w7ElSEk-21U',null,stream_context_create(array(
		'http' => array(
			'protocol_version' => 1.1,
			'user_agent'       => 'Ariska',
			'method'           => 'POST',
			'header'           => "Content-type: application/json\r\n".
								  "Connection: close\r\n" .
								  "Content-length: " . strlen($json_data) . "\r\n",
			'content'          => $json_data,
		),
	)));
	 
	$json = json_decode($post, TRUE);
	$linkreminder=$json['id'];

$linkreminder2 = '

KLIK UNTUK MEMBUAT REMINDER:
'.$linkreminder;
$txtmessage = $linkreminder2.'

'.$txtmessage;
syslog(LOG_INFO, $txtmessage);	
	}	

//*/


					$sqlx = "UPDATE MASTER_MEMO SET STATUS = 'VALID' WHERE trim(NOMOR) = trim('$nomor') AND STATUS IS NULL";
					mysql_query($sqlx);// or die(mysql_error());

					if ($masterstatus !== 'BATAL' && $masterstatus !== 'EXPIRE' && $masterstatus !== 'TOLAK' && $kirimmemo ) {		
						$cc = strtolower($cc);
						$to = strtolower($to);

						$tglisi = date('Ymd H:i:s');
						$message_body =  $txtmessage;
						$mail_options = [   
						 "sender" => "systemsm@siantarmaju.com", 
						 "to" => $tos,
						 "cc" => $ccs, 
						 "subject" => $mailsubject, 
						 "textBody" => $message_body
						 ];

						try {    
							$emaildikirim = new Message($mail_options);    
							$emaildikirim->send();
							echo " selesai kirim ".$to."<br >";
						} 
							catch (InvalidArgumentException $e) {
							echo $e;	
						}
					} //end of if ($masterstatus !== 'BATAL' && $masterstatus !== 'EXPIRE' && $masterstatus !== 'TOLAK' && $kirimmemo ) line 401 			
			
				} // end of if ($numberofall == $numberofvalid && $numberofvalid > 0) line 363

			} // end of if ($row2['belumdivalidasi'] == 0 && $rowjwbtdk['tolak'] == 0 && $tidakmenjawab == false) line 302
			else if( $numbernotvalid > 0  && $tidakmenjawab == false) { // untuk proses jawaban tolak
				$master_id = $rowjwbtdk['master_id'];					
				$mailsubject = 'INFO/SM/MEMO/'.$nomornya;

$txtmessage = '
DITOLAK : MEMO NOMOR : '.$nomornya.'

DIBUAT OLEH
NAMA/NIK :'
.$nama.'/'.$nik.'
TANGGAL JAM:'
.$waktu.'
';
				
				$message_body =  $txtmessage;
				$mail_options = [   
				 "sender" => "systemsm@siantarmaju.com", 
				 "to" => $backsend,   
				 "subject" => $mailsubject, 
				 "textBody" => $message_body
				 ];
				try {    
					$emaildikirim = new Message($mail_options);    
					$emaildikirim->send();
					echo " selesai kirim ".$backsend." <br >";
				} 
				catch (InvalidArgumentException $e) {
				echo $e;	
				}
				$sql = "UPDATE MASTER_MEMO SET STATUS = 'TOLAK' WHERE trim(NOMOR) = trim('$nomor')";
				mysql_query($sql);// or die(mysql_error());					
				$sql = "COMMIT";			
				mysql_query($sql);// or die(mysql_error());						
			} // end of else if( $rowjwbtdk['tolak'] > 0  && $tidakmenjawab == false) line 427
		} // end of if (substr($nomor,0,3) !== 'MIS' && strlen($nomor) == 11 && $tidakmenjawab == false) line 141  		
	//} // end of  foreach ($isi as $value) line 30 	
	} // 
}  // end of  while ( false !== $row2 = mysql_fetch_assoc($resultSQL) ) line 19

syslog(LOG_INFO, "selesai approval by manual code");




}  else { 	echo 'MASUKAN ULANG KODE KARENA KODE SEBELUMNMYA TIDAK BENAR'; }	
}
} 
//echo $nomor; exit;
?>  
<form action="memoviewtest.do" method="post" name="adminForm" id="adminForm">
<a href="lihatmemo.do">KEMBALI</a>
<table width=100% BORDER="0">
<?PHP
$result = '';
if ($nomor !== '' && !empty($nomor)){
$sql = "SELECT M.NIK, M.NAMA, 'MENUNGGU' AS STATUS, D.VALIDATOR, '' AS KONFIRMASI, M.alamatemail as emailao 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL IS NULL
				
				UNION ALL
				SELECT M.NIK, M.NAMA, 'SETUJU' AS STATUS, D.VALIDATOR, D.KETERANGAN AS KONFIRMASI, M.alamatemail as emailao 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL = SUBSTRING(KODE,1,6)

				UNION ALL
				SELECT M.NIK, M.NAMA, 'TOLAK' AS STATUS, D.VALIDATOR, D.KETERANGAN AS KONFIRMASI, M.alamatemail as emailao 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL = SUBSTRING(KODE,7,6)				
        ";

				
$result = mysql_query($sql);
	//C14DS000271
}
?>
NOMOR MEMO <input type="text" name="nomornya" value="<?php echo $nomor; ?>">
<?php
while ($row = mysql_fetch_assoc($result)) {
?>
<tr> 
<td><?PHP	echo $row["NAMA"].'/'.$row["NIK"].' '; ?></td>
<td></td><td> <?PHP  echo ' MASUKAN KODE : ( samakan huruf kapital atau tidaknya)' ; ?></td>
</td><td></td><td><?PHP	Echo $row["KONFIRMASI"].'<br />'; ?>
<td><input type="text" name="tbox[]" value="">
<input type="hidden" name="tbox1[]" value="<?php echo $row["NIK"]; ?>">
<input type="hidden" name="tbox2[]" value="<?php echo $row["emailao"]; ?>">
</td>
<?php } ?>
<td><input class="button" type="submit" value="SIMPAN"  /></td>
<tr>
</table>
	<tr><td><a href="lihatmemo.do">KEMBALI</a></td></tr>
	<?php $feedback = "https://mail.google.com/mail/u/0/?view=cm&fs=1&tf=1&to=systemsm@siantarmaju.com&su=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>
</form>	
