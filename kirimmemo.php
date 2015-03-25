<?php
ob_start();
//mail-by : m3kw2wvrgufz5godrsrytgd7.apphosting.bounces.google.com 
if (isset($_REQUEST['pakaiduedate'])) {
$pakaiduedate = isset($_REQUEST['pakaiduedate']) ;
}
if (!empty($pakaiduedate)){
	if (isset($_REQUEST['mulaiberlaku'])) {
		$tanggalduedate = $_REQUEST['mulaiberlaku'];
	}
	if (!empty($tanggalduedate)){
		$tgl= explode('-',$tanggalduedate);
		$th = $tgl[0];
		$bln = $tgl[1];
		$hari = $tgl[2];
		$tanggalend = date('Ymd', mktime(0,0,0,$bln,$hari+1,$th));
		$tanggalduedate = str_replace('-','',$tanggalduedate);	
	} else {
		echo "tidak bisa disimpan : TANGGAL PENYELESAIAN tidak dipilih"; 
		exit;
	}
} 
date_default_timezone_set('Asia/Bangkok');
include_once 'mosHTML.php';
require_once 'google/appengine/api/mail/Message.php';
require_once 'google/appengine/api/log/LogService.php';
use google\appengine\api\log\LogService;
use google\appengine\api\mail\Message;


mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');

// print_r($_REQUEST);
IF (isset($_REQUEST['perihal'])) {
$perihal = $_REQUEST['perihal'];
} ELSE { $perihal = '' }
IF (isset($_REQUEST['editor1'])) {
$isimemo = $_REQUEST['editor1'];
} ELSE { EXIT; }
IF (isset($_REQUEST['nama']) ){
$nama =  $_REQUEST['nama'];
} ELSE { EXIT; }
IF (isset($_REQUEST['nik']) ){
$nik =  $_REQUEST['nik'];
} ELSE { EXIT; }
IF (isset($_REQUEST['bagian'])) {
$bagian =  $_REQUEST['bagian'];
} ELSE { EXIT; }
IF (isset($_REQUEST['email']) ){
$email =  $_REQUEST['email'];
}

 if ($nama === '') {
 //header('Location: http://lihatmemo.appspot.com'); 
 exit;
 }

IF (isset($_REQUEST['images'])) {
 if  ($_REQUEST['images'] === '' ) {
 echo "tidak bisa disimpan : Kepada tidak boleh kosong"; 
 exit;
 }
 
}

IF (isset($_REQUEST['authors3'])) {
 if  ($_REQUEST['authors3'] === '' ) {
 echo "tidak bisa disimpan : Menyetujui tidak boleh kosong";
 exit;
 }

}

 if ($isimemo === '' ) {
 echo "tidak bisa disimpan : Memo tidak boleh kosong";
 exit; 
 }

//$mailsubject .= "APPROVAL/".strtoupper($dept)."/MEMO/".$nomormaster;
$waktu = date("j F Y, H:i:s"); 
// 'Senin 12 12 2012';//strftime("%d-%b-%Y %H:%M:%S",  date( 'Y-m-d' )  ); 
 
$arremailto = explode(",", $_REQUEST['images']); 	
$addto = implode( ",",$arremailto); 
$to =  '("'.implode('", "', $arremailto).'")';
	$queryto = " select nama FROM MASTER_USER_ACCOUNT where trim(alamatemail) in $to " ;
	$arrsendto = mysql_query($queryto);	
	$arrto = array();
	while (false !== $row = mysql_fetch_object($arrsendto))
	{   
		$arrto[] = $row->nama;
	}  
	$nameto =implode(',
',$arrto);
//*/

$arrvalidator = explode(",", $_REQUEST[ 'authors3']); 	
$addval = implode( ",",$arrvalidator); 
$validator =  '("'.implode('", "', $arrvalidator).'")';
///*
	$queryval = " select nama FROM MASTER_USER_ACCOUNT where trim(alamatemail) in $validator " ;
	$arrsendval = mysql_query($queryval);	
	$arrval = array();	
	while (false !== $row = mysql_fetch_object($arrsendval))
	{   
		$arrval[] = $row->nama;
	} 
	$nameval =implode(',
',$arrval);
//*/
	$nomormaster = $DOZPASSGEN->genPassword('mix',20);
	
// /*
	IF ($nama !== '' ) {
 	$query = "call ADDMEMO('$nama','$bagian','$nik', '$email' ,'$isimemo','$addto','$isimemo'
	,'$addval','$email','$nomormaster', '$perihal')" ;
	$addmemo = mysql_query($query);
	$query = "commit" ;
 	$addmemo = mysql_query($query);		
	}
	
	$query = "select master_id,nomor,tanggal 
	from MASTER_MEMO
	where 1=1
	AND insertid = '$nomormaster'
	AND nama = '$nama'
	AND nik = '$nik'	
	" ;
	
	$hasiladdmemo = mysql_query($query);	
 	while ($addmemoresult = mysql_fetch_assoc($hasiladdmemo) ) {
	$masterid=$addmemoresult['master_id'];
	$nomormemo =  $addmemoresult['nomor'];
	$tanggalmemo = $addmemoresult['tanggal'];	
	}
	//print_r($addmemoresult);
 if (!isset($nomormemo)){
	echo "memo tidak bisa disimpan silahkan membuat ulang";
	exit;	

 }	 

 if (!isset($masterid)){
	echo "memo tidak bisa disimpan silahkan membuat ulang";
	exit;	
 }	 


 if  ($nomormemo === '' ) {
 echo "memo tidak bisa disimpan silahkan membuat ulang";
 exit;
 }

 if  ($masterid === '' ) {
 echo "memo tidak bisa disimpan silahkan membuat ulang";
 exit;
 }
 
//*/	
$subjectmemo = 'APPROVAL/'.strtoupper($bagian) .'/MEMO/'.$nomormemo	;
///*
$isiremindermemolong ='https://mail.google.com/mail/u/0/?shva=1#search/MEMO+'.$nomormemo;
$article1 = new stdClass();
$article1->longUrl = $isiremindermemolong;
$json_data1 = json_encode($article1);
$post1 = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyArMgHiMThCRwfNa6XrPKkSZ7B_IMaFIr4',null,stream_context_create(array(
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
//*/
// /*
$txtmessage1 = '
MEMO NOMOR :'.$masterid.'/'.$nomormemo.'
PERIHAL :'.$perihal.'
KEPADA :'.'
'.$nameto.'
DIBUAT OLEH :'.'
NAMA/NIK :'.$nama.'/'.$nik.'
TANGGAL JAM:'.$waktu.'
MEMO :'.'
'.$isimemo.'
PERSETUJUAN:'.'
'.$nameval.'  
';
//*/
///* duedate

if (!empty($pakaiduedate)){
	IF (!empty($tanggalduedate)) {
	$query = "insert into REMINDER_MEMO VALUES('$nomormemo','$tanggalduedate')" ;
	syslog(LOG_INFO, $query);
	$addmemo = mysql_query($query);
	$query = "commit" ;
	$addmemo = mysql_query($query);		
	}
}

$linkperihal = str_replace(' ','+',$perihal);
if (!empty($pakaiduedate)){
$linkreminderlong = 'https://www.google.com/calendar/render?action=TEMPLATE&text=Reminder+memo:'.$linkperihal
.'&dates='.$tanggalduedate.'/'.$tanggalend
.'&details='.$isiremindermemo
.'&sf=true&output=xml'
;

$article = new stdClass();
$article->title = "An example article";
$article->longUrl = $linkreminderlong;
 
$json_data = json_encode($article);
 
//$post = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC_dNVDslL-yksuVeduN7v7w7ElSEk-21U',null,stream_context_create(array(
$post = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyArMgHiMThCRwfNa6XrPKkSZ7B_IMaFIr4',null,stream_context_create(array(
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

$linkreminder='Klik untuk buat reminder :
 '.$linkreminder;

} else {
$linkreminder = '';
}

/*
if (!empty($pakaiduedate)){
	IF (!empty($tanggaldudate)) {
	$query = "insert into REMINDER_MEMO VALUES('$nomormemo','$tanggaldudate')" ;
	syslog(LOG_INFO, $query);
	$addmemo = mysql_query($query);
	$query = "commit" ;
	$addmemo = mysql_query($query);		
	}
}
//*/

// mulai generate per validator
foreach( $arrvalidator as $emailvalidator ) {

$mailcheck2 =  $DOZPASSGEN->genPassword('mix',6);									
$reject = $DOZPASSGEN->genPassword('mix',6);    

while ($mailcheck2 ===  $reject ){
	$reject = $DOZPASSGEN->genPassword('mix',6);    
}
$txtmessage = $linkreminder.'
 '.$txtmessage1.' 
KODE:
'."Y:$mailcheck2".'
# #
'."T:$reject".'
# #'.'
REPLY KE systemsm@siantarmaju.com DENGAN COPY PASTE Y:KODE atau T:KODE'.'
JIKA DIPERLUKAN KONFIRMASI, TULISKAN KONFIRMASI DI ANTARA TANDA #
';		
//*/
IF ($nama !== '' ) {

		$querydetail = "call ADDDETAILMEMO('$masterid','$nomormemo','$mailcheck2$reject', '$emailvalidator',null)" ;
		$adddetailmemo = mysql_query($querydetail);
		$querydetail = "commit" ;
		$adddetailmemo = mysql_query($querydetail);	
}
		
$message_body =  $txtmessage;

$mail_options = [   
 "sender" => "systemsm@siantarmaju.com", 
 "to" => $emailvalidator,   
 "subject" => $subjectmemo, 
 "textBody" => $message_body
 ];
 
 
 IF ($nama !== '' ) {
 try {    
 $emaildikirim = new Message($mail_options);    
 $emaildikirim->send();
 echo "Selesai mengirim ke ".$emailvalidator ;
  
 } 
 catch (InvalidArgumentException $e) {
 echo $e;	
 }
 }
 }
	
	$perihal = '';
	$isimemo = '';
	$nama =  '';
	$nik =  '';
	$bagian = '';
	$email =  '';

	$querydetail = "DELETE FROM MASTER_MEMO WHERE NAMA = ''" ;
	$adddetailmemo = mysql_query($querydetail);
	$querydetail = "DELETE FROM DETAIL_MEMO WHERE VALIDATOR = ''" ;
	$adddetailmemo = mysql_query($querydetail);
	
?>
	<table>
	<tr><td><a href="lihatmemo.do">KEMBALI</a></td></tr>
	<?php $feedback = "https://mail.google.com/mail/u/0/?view=cm&fs=1&tf=1&to=systemsm@siantarmaju.com&su=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>	
	</table>
<?php	
ob_flush();
// exit;
?>
