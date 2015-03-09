<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>MEMO INTERNAL SIANTAR MADJU</title>
<link rel="shortcut icon" href="static/favicon.ico">

	<?php	
		IF (isset($_REQUEST['act']))
		{
			$act = $_REQUEST['act'];
			$site_url = "https://mail.google.com/mail/u/0/?shva=1#search/MEMO+$act";			
			echo "<script language=\"JavaScript\">{ 
			var newWin = window.open();
			var windowName = 'userConsole'; 
			var popUp = window.open('$site_url', windowName, 'width=1000, height=700, left=24, top=24, scrollbars, resizable');
			if (popUp == null || typeof(popUp)=='undefined') { 	
				alert('Please disable your pop-up blocker.'); 
				location.href=\"$site_url\"; 
				self.focus(); 				
			} 
			else { 	
				popUp.focus();
				newWin.location = \"$site_url\";				
			}									 			
			}</script>";			
		}	
	?>
</head>	
<body>
<?php
date_default_timezone_set('Asia/Bangkok');
$user = array();
require_once 'google/appengine/api/users/UserService.php';
use google\appengine\api\users\UserService;
?>
<form action="lihatmemo.php" method="post">
<div>
 <input type="text" name="act" value=""/>
 <input type="submit" value="Search in memo email" />
</div>
</form>
<table>
<tr><td><a href="buatmemo.do">BUAT MEMO</a></td></tr>
</table>
<?php
$user = UserService::getCurrentUser();
if ($user) {
	$emailpembuat = $user->getNickname();
?>
  <span class="username"><?php echo $user->getNickname(); ?></span>
  <a href="
	<?php 
	  echo UserService::createLogoutURL('', "google.com");
	 ?>
	 ">log out</a>
<?php } else { 
?>
  <div class="module">
  <a href="
	<?php 
	echo UserService::createLoginURL('');
	?>
	">SILAHKAN LOG IN DISINI UNTUK MENGAKSES</a>
  </div>
<?php } 

	if (isset($user)) {
		$useremail = $user->getNickname();
	} else {			
		//$useremail = 'tester@siantarmaju.com';
		EXIT;
	}

	$cloudsql = mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
	mysql_select_db('SIMIS');

	require_once('class.eyemysqladap.inc.php');
	require_once('class.MultiGrid.inc.php');	

	//echo $useremail."<br />";

	$sqluser = "select nama,nik,bagian from master_user_account where alamatemail like '%$useremail%' ";
	$result = mysql_query($sqluser);
	$userdata = mysql_fetch_row($result);
	$username = $userdata[0];
	$usernik = $userdata[1];
	$userbagian = $userdata[2];
	//mysql_close($cloudsql);

	if (!isset($dbtask)) {
		$dbtask = new EyeMySQLAdap(':/cloudsql/database-sm:database-sm', 'root', '', 'SIMIS');				
	}
	$filter = " ((STATUS IS NULL) AND (KETERANGAN = '$useremail')) ";
	
	$grid_lpb1 = new MultiGridcls($dbtask,'gridlpbno1');				
	$grid_lpb1->setQuery("NOMOR, NAMA, PERIHAL,  TIMESTAMPADD(HOUR,7,TANGGAL) AS TANGGAL, STATUS
				","MASTER_MEMO","NOMOR",$filter);
	//$grid_lpb->hidefooter(true);	
	$grid_lpb1->allowFilters(true);
	$grid_lpb1->setResultsPerPage(10);			
	$grid_lpb1->showReset();
	$grid_lpb1->showRowNumber();	
	$grid_lpb1->setColumnType('NOMOR', MultiGridcls::TYPE_HREF, "detailmemo.do?NOMOR=%NOMOR%");
	$grid_lpb1->setColumnType('STATUS', MultiGridcls::TYPE_ARRAY, array('' => 'Menunggu', 'VALID' => 'Disetujui' , 'TOLAK' => 'Ditolak' , 'BATAL' => 'Batal', 'EXPIRE' => 'Expire'  )); 
	$grid_lpb1->setColumnType('NAMA', MultiGridcls::TYPE_HREF, "memoviewtest.do?nomornya=%NOMOR%");
	$grid_lpb1->printTable();
	//$dbtask->freeResult();
	//$dbtask->close; 
	
	
	if (!isset($dbtaskgrid_lpb)) {
		$dbtaskgrid_lpb = new EyeMySQLAdap(':/cloudsql/database-sm:database-sm', 'root', '', 'SIMIS');				
	}
	$filter2 = " 1=1 	
	AND ( ((( `TO` LIKE '%$useremail%') OR 				
				 (KETERANGAN LIKE '%$useremail%') OR  
				 (VALIDATOR LIKE '%$useremail%'))
				 AND (NAMA <> '$username') 	
				 AND (STATUS IS NOT NULL)  	
				) 				
				OR 
				(
				 (STATUS IS NOT NULL) AND 
				 (NAMA = '$username')	
				)		
			)
	";
	
	$grid_lpb = new MultiGridcls($dbtaskgrid_lpb,'gridlpbno2');					
	$grid_lpb->setQuery("NOMOR, NAMA, PERIHAL, TIMESTAMPADD(HOUR,7,TANGGAL) AS TANGGAL, STATUS
				","MASTER_MEMO","NOMOR",$filter2);
	//$grid_lpb->hidefooter(true);	
	$grid_lpb->allowFilters(true);
	$grid_lpb->setResultsPerPage(10);			
	$grid_lpb->showReset();
	$grid_lpb->showRowNumber();	
	$grid_lpb->setColumnType('NOMOR', MultiGridcls::TYPE_HREF, "detailmemobaca.do?NOMOR=%NOMOR%");
	$grid_lpb->setColumnType('STATUS', MultiGridcls::TYPE_ARRAY, array('' => 'Menunggu', 'VALID' => 'Disetujui' , 'TOLAK' => 'Ditolak' , 'BATAL' => 'Batal', 'EXPIRE' => 'Expire'  )); 
	$grid_lpb->printTable();	
	//$dbtaskgrid_lpb->freeResult();
	//$dbtaskgrid_lpb->close();
	
?>

	<table>
	<tr><td><a href="buatmemo.do">BUAT MEMO</a></td></tr> 
	<?php $feedback = "https://mail.google.com/mail/u/0/?view=cm&fs=1&tf=1&to=systemsm@siantarmaju.com&su=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>		
	</table>
</body>
</html>
