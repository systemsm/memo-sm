<?php 
date_default_timezone_set('Asia/Bangkok');
$user = array();
require_once 'google/appengine/api/users/UserService.php';
use google\appengine\api\users\UserService;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>MEMO INTERNAL SIANTAR MADJU</title>
<style type="text/css">
body { font: 0.8em Arial; }

/* Datagrid Table */
table.tbl { width: 100%; border: 2px solid #c3daf9; font-size: 0.9em; clear: both; }
td.tbl-header { text-align: center; padding: 3px; font-weight: bold; border-bottom: 2px solid #c3daf9; }
tr.tbl-footer {}
table.tbl-footer { font-size: 1em; }
tr.tbl-row {}
tr.tbl-row:hover { background: #EBFFFF; } /* Old color: #E9E9E9 */
tr.tbl-row-even { background: #f4f4f4; }
tr.tbl-row-odd { background: white; }
tr.tbl-row-highlight:hover { background: #fffba6; cursor: pointer; }
td.tbl-nav { height: 20px; border-top: 2px solid #c3daf9; color: #4D4D4D; }
td.tbl-pages { text-align: center; }
td.tbl-row-num { text-align: right; }
td.tbl-cell {}
td.tbl-controls { text-align: center; }
td.tbl-found {}
td.tbl-checkall {}
td.tbl-page { text-align: right; }
td.tbl-noresults { font-weight: bold; color: #9F0000; height: 45px; text-align: center; }
span.tbl-reset { margin: 5px 5px; }
img.tbl-reset-image { margin-right: 5px; border: 0; }
span.tbl-create { margin: 5px 0; }
img.tbl-create-image { margin-right: 5px; border: 0; }
div.tbl-filter-box {}
img.tbl-arrows { border: 0; }
img.tbl-order-image { margin: 0 2px; border: 0; }
img.tbl-filter-image { border: 0; }
img.tbl-control-image { border: 0; }
span.page-selected { color: black; font-weight: bold; }
input.tbl-checkbox {}
</style>

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
<form action="coba.php" method="post">
 <td>
 <input type="text" name="act" value=""/>
 </td>
 <td>	
 <input type="submit" value="Search in memo email" />
  </td>
 </form>	

	<table>
	<td><a href="buatmemo.do">BUAT MEMO</a></td> 
	</table>

<?php $user = UserService::getCurrentUser();
if ($user) {
	$emailpembuat = $user->getNickname();
?>
  <span class="username"><?php echo $user->getNickname(); ?></span>
  <a href="<?php echo UserService::createLogoutURL('', "google.com");?>">log out</a>
<?php } else { 
?>
  <div class="module">
  <a href="<?php echo UserService::createLoginURL('');?>">SILAHKAN LOG IN DISINI UNTUK MENGAKSES</a>
  </div>
<?php } 
mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');

require_once('class.eyemysqladap.inc.php');
require_once('class.MultiGrid.inc.php');	


	if (isset($user)) {
		$useremail = $user->getNickname();
	} else {			
		//$useremail = 'tester@siantarmaju.com';
		EXIT;
	}

	//echo $useremail."<br />";

	$sqluser = "select nama,nik,bagian from master_user_account where alamatemail like '%$useremail%' ";
	$result = mysql_query($sqluser);
	$userdata = mysql_fetch_row($result);
	$username = $userdata[0];
	$usernik = $userdata[1];
	$userbagian = $userdata[2];

	$dbtask = new EyeMySQLAdap(':/cloudsql/database-sm:database-sm', 'root', '', 'SIMIS');				
	$filter = " ((STATUS IS NULL) AND (KETERANGAN = '$useremail')) ";
	$grid_lpb1 = new MultiGridxno1($dbtask);				
	$grid_lpb1->setQuery("NOMOR, NAMA, PERIHAL,  TIMESTAMPADD(HOUR,7,TANGGAL) AS TANGGAL, STATUS
				","MASTER_MEMO","NOMOR",$filter);
	//$grid_lpb->hidefooter(true);	
	$grid_lpb1->allowFilters(true);
	$grid_lpb1->setResultsPerPage(10);			
	$grid_lpb1->showReset();
	$grid_lpb1->showRowNumber();	
	$grid_lpb1->setColumnType('NOMOR', MultiGridxno1::TYPE_HREF, "detailmemo.do?NOMOR=%NOMOR%");
	$grid_lpb1->setColumnType('STATUS', MultiGridxno1::TYPE_ARRAY, array('' => 'Menunggu', 'VALID' => 'Disetujui' , 'TOLAK' => 'Ditolak' , 'BATAL' => 'Batal', 'EXPIRE' => 'Expire'  )); 
	$grid_lpb1->setColumnType('NAMA', MultiGridxno1::TYPE_HREF, "memoviewtest.do?nomornya=%NOMOR%");
	$grid_lpb1->printTable();
	
	$dbtaskgrid_lpb = new EyeMySQLAdap(':/cloudsql/database-sm:database-sm', 'root', '', 'SIMIS');				
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
	$grid_lpb = new MultiGridxno2($dbtaskgrid_lpb);				
	$grid_lpb->setQuery("NOMOR, NAMA, PERIHAL, TIMESTAMPADD(HOUR,7,TANGGAL) AS TANGGAL, STATUS
				","MASTER_MEMO","NOMOR",$filter2);
	//$grid_lpb->hidefooter(true);	
	$grid_lpb->allowFilters(true);
	$grid_lpb->setResultsPerPage(10);			
	$grid_lpb->showReset();
	$grid_lpb->showRowNumber();	
	$grid_lpb->setColumnType('NOMOR', MultiGridxno2::TYPE_HREF, "detailmemobaca.do?NOMOR=%NOMOR%");
	$grid_lpb->setColumnType('STATUS', MultiGridxno2::TYPE_ARRAY, array('' => 'Menunggu', 'VALID' => 'Disetujui' , 'TOLAK' => 'Ditolak' , 'BATAL' => 'Batal', 'EXPIRE' => 'Expire'  )); 
	$grid_lpb->printTable();	
?>

	<table>
	<tr><td><a href="buatmemo.do">BUAT MEMO</a></td></tr> 
	<?php $feedback = "mailto:systemsm@siantarmaju.com?subject=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>		
	</table>
</body>
</html>