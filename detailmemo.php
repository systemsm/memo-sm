<?php
date_default_timezone_set('Asia/Bangkok');
require_once 'google/appengine/api/users/UserService.php';
use google\appengine\api\users\UserService;

mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');
//print_r($_REQUEST);
//echo "<br />";
$nomor =  $_REQUEST['NOMOR'];
?>  
<head>
<title>MEMO INTERNAL SIANTAR MADJU</title>
<style type="text/css">
	td.left, td.right { width: 5px;	}
	div.wrapper { margin: 0 auto; width: 1024px;}
</style>
</head>

<body id="page_bg" class="f-smaller">

<?php $user = UserService::getCurrentUser();

if ($user) {
	$emailpembuat = $user->getNickname();
?>
  <span class="username"><?php echo $user->getNickname(); ?></span>
  <a href="<?php echo UserService::createLogoutURL('', "google.com");?>">log out</a>
<?php } else { 
?>
  <div class="module">
  <a href="<?php echo UserService::createLoginURL('');?>">SILAHKAN LOG IN</a>
  </div>
<?php } ?>	

<form action="memobatal.do" method="post" name="adminForm" id="adminForm">
<div class="moduletable">
<?php
$sqlM = "SELECT M.NOMOR, M.NAMA, M.PERIHAL, TIMESTAMPADD(HOUR,7,M.TANGGAL) as TANGGAL, M.MEMO
				FROM MASTER_MEMO M
				WHERE 1=1 
				AND M.NOMOR = '$nomor' 
        ";

$resultM = mysql_query($sqlM);

if (!$resultM) {
    echo "Could not successfully run query ($sql) from DB: " . mysql_error();
    exit;
}

if (mysql_num_rows($resultM) == 0) {
    echo "No rows found, nothing to print so am exiting";
    exit;
}
while ($rowM = mysql_fetch_assoc($resultM)) {
$nomormemo = $rowM["NOMOR"]; 
?>
	<table width=100% BORDER="0">
		<tr>
			<th width=14%> <div align="left">MEMO<br /></div></th>
			<th width=1%> <div align="left"><br /></div></th>
			<th width=80%> <div align="left"><br /></div></th>
		</tr>
		<tr>
		<td>NOMOR</td><td></td><td><?php echo $rowM["NOMOR"].' ' ; ?></td>
		</tr>
		<tr>
		<td>PEMBUAT</td><td></td><td><?php echo $rowM["NAMA"].' ' ; ?></td>
		</tr>
		<tr>
		<td>TANGGAL</td><td></td><td><?php echo $rowM["TANGGAL"].' ' ; ?></td>
		</tr>
		<tr>
		<td>PERIHAL</td><td></td><td><?php echo $rowM["PERIHAL"].' ' ; ?></td>
		</tr>
		<tr>
		<td>MEMO</td><td></td><td></td>
		</tr>
		<tr>
		<td></td><td></td><td><?php echo $rowM["MEMO"].' ' ; ?></td>
		</tr>

	</table>
<?php }	 ?>
	<table width=100% BORDER="0">
		<tr>
			<th width=25%> <div align="center">VALIDATOR<br /></div></th>
			<th width=2%> <div align="center"><br /></div></th>
			<th width=16%> <div align="center"><br />KEPUTUSAN</div></th>			
			<th width=2%> <div align="center"><br /></div></th>				
			<th width=55%> <div align="center"><br /></div></th>				
		</tr>

<?PHP

$sql = "SELECT M.NIK, M.NAMA, 'MENUNGGU' AS STATUS, D.VALIDATOR, '' AS KONFIRMASI 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL IS NULL
				
				UNION ALL
				SELECT M.NIK, M.NAMA, 'SETUJU' AS STATUS, D.VALIDATOR, D.KETERANGAN AS KONFIRMASI 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL = SUBSTRING(KODE,1,6)

				UNION ALL
				SELECT M.NIK, M.NAMA, 'TOLAK' AS STATUS, D.VALIDATOR, D.KETERANGAN AS KONFIRMASI 
				FROM DETAIL_MEMO D
				INNER JOIN MASTER_USER_ACCOUNT M ON M.alamatemail like D.VALIDATOR
				WHERE 1=1 
				AND D.NOMOR = '$nomor' 
				AND D.REPLYEMAIL = SUBSTRING(KODE,7,6)				
        ";

$result = mysql_query($sql);

if (!$result) {
    echo "Could not successfully run query ($sql) from DB: " . mysql_error();
    exit;
}

if (mysql_num_rows($result) == 0) {
    echo "No rows found, nothing to print so am exiting";
    exit;
}

while ($row = mysql_fetch_assoc($result)) {

?>
<tr>
<td><?PHP	echo $row["NAMA"].'/'.$row["NIK"].' '; ?></td>
<td></td><td> <?PHP  echo $row["STATUS"].' ' ; ?></td>
</td><td></td><td><?PHP	Echo $row["KONFIRMASI"].'<br />'; ?>
</td><tr>
<?php
}

//mysql_free_result($result);

?> 
	</table>
	<table>
	<td><a href="lihatmemo.do">KEMBALI</a></td> 	
	</table>
	<table>
	<td><a href="memobatal.do?NOMOR=<?php echo $nomormemo; ?>">BATALKAN MEMO <?php  echo $nomormemo; ?></a></td> 	
	</table>	
	</div>	
	<div >
		<div align="center">&copy; 2013 SIANTAR MADJU,PT</div>
		<div align="center">2014.1.0 (ALPHA)</div>
	</div>
</form>	
</body>
</html><!-- 1377244644 -->