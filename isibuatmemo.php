<table>
<tr><td><a href="lihatmemo.do">KEMBALI</a></td></tr> 
</table>
<?php
date_default_timezone_set('Asia/Bangkok');
include_once 'mosHTML.php';
require_once 'google/appengine/api/users/UserService.php';
require_once 'google/appengine/api/mail/Message.php';
use google\appengine\api\mail\Message;
use google\appengine\api\users\UserService;

mysql_connect(':/cloudsql/database-sm:database-sm', 'root', '');
mysql_select_db('SIMIS');

$sqluser = "select nama as text, alamatemail as value from MASTER_USER_ACCOUNT where aktif = 'Y' order by 1";
$result = mysql_query($sqluser);
$authors = array();
while (false !== $row = mysql_fetch_object($result))
{   
 $totaluser[] = $row; 
}  
$authors = array_merge( $authors, $totaluser);

if (isset($user)) {
$useremail = $user->getNickname();
} else {
$useremail = 'tester@siantarmaju.com';
}

$sqluser = "select nama,nik,bagian from MASTER_USER_ACCOUNT where alamatemail like '%$useremail%' ";
$result = mysql_query($sqluser);
$userdata = mysql_fetch_row($result);
$username = $userdata[0];
$usernik = $userdata[1];
$userbagian = $userdata[2];

$id = 'test123456';
$option = 'com_lpe';
//$loginrow = array('NIK'=>'20080045','NAMA'=>'ARISKA BUDIJUWONO','BAGIAN'=>'MIS','EMAIL'=>'ariskabudijuwono@siantarmaju.com');
$loginrow = array('NIK'=>$usernik,'NAMA'=>$username,'BAGIAN'=>$userbagian,'EMAIL'=>$useremail);

$row = '';
$menus = '';

$filter_authorid = array();
$filter_authorid2 = array();
$filter_authorid3 = array();
$filter_authorid4 = array();
$filter_authorid5 = array();
$filter_authorid6 = array();

$lists = array();

$lists['filter_authorid']	= mosHTML::selectList( $authors, 'filter_authorid', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $filter_authorid );
$lists['filter_authorid2']	= mosHTML::selectList( $authors2, 'filter_authorid2', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $filter_authorid2 );
$authors3 = $authors;

$lists['filter_authorid3']	= mosHTML::selectList( $authors3, 'filter_authorid3', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $filter_authorid3 );
$lists['filter_authorid4']	= mosHTML::selectList( $authors4, 'filter_authorid4', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $filter_authorid4 );

$params = '';

include_once('memoscript.php');

?>
<form action="kirimmemo.do" method="post" name="adminForm" id="adminForm">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td valign="top">
<table class="adminform">
<tr>	
<td colspan="2">					
<table width="100%">						
<tr>							
<td valign="top">
<div >
KEPADA :
<br />
<?php 
echo $lists['filter_authorid']; 
?>
</div>
</td>
<td >
<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','filter_authorid','filter_authorid2')" title="Add"/>
<br/>
<input class="button" type="button" value="<<" onclick="addSelectedToList('adminForm','filter_authorid2','filter_authorid')" title="Remove"/>
</td>
<td >
<div>
KEPADA:
<br />
<?php 
echo $lists['filter_authorid2'];
?>
<br />
</div>
</td>

</tr>
</table>

<table width="100%">
<tr>
<td valign="top">
<?php echo 'NAMA : ' ;?>
</td>
<td valign="top">
<?php echo $loginrow['NAMA']; ?>
</td>          
</tr>
<tr>
<td  valign="top">
<?php echo 'NIK  : ';?>
</td>
<td  valign="top">
<?php echo $loginrow['NIK']; ?>
</td>          
</tr>
<tr>
<td valign="top">
<?php echo 'BAGIAN : ';?>
</td>
<td valign="top">
<?php echo $loginrow['BAGIAN']; ?>
</td>          
</tr>				

<table width="100%">
<tr>
<td>						
<tr>
<td valign="top" align="left" colspan="2">
PERIHAL : <br />
</td></tr><tr>
<td><textarea class="text_area" cols="30" rows="1" style="width: 350px; height: 25px" name="perihal" id="perihal"><?php echo ''; ?></textarea></td>
</td>
</tr>
<tr>
<td valign="top" align="left" colspan="2">
MEMO : <br />
</td></tr><tr>
<td><textarea class="text_area" cols="30" rows="10" style="width: 350px; height: 60px" name="editor1" id="editor1"><?php echo ''; ?></textarea></td>
</td>
</tr>
</table>						

<table width="100%">
<tr>							
<td valign="top">
<div>
MEMINTA PERSETUJUAN:
<br />
<?php 
echo $lists['filter_authorid3'];
//echo $lists['imagefiles'];										
?>
</div>
</td>
<td>
<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','filter_authorid3','filter_authorid4')" title="Add"/>
<br/>
<input class="button" type="button" value="<<" onclick="addSelectedToList('adminForm','filter_authorid4','filter_authorid3')" title="Remove"/>
</td>
<td >
<div>
MENYETUJUI:
<br />
<?php 
echo $lists['filter_authorid4'];
?>
<br />
</div>
<input type="hidden" name="images"  id="images" />
<input type="hidden" name="authors3" id="authors3"/>
<input type="hidden" name="nama" value="<?php echo $loginrow['NAMA']; ?>" />
<input type="hidden" name="nik" value="<?php echo $loginrow['NIK']; ?>" />
<input type="hidden" name="bagian" value="<?php echo $loginrow['BAGIAN']; ?>" />		
<input type="hidden" name="email" value="<?php echo $loginrow['EMAIL']; ?>" />
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />

</td>

</tr>
</table>

<table width="100%">
 	<tr>
		<td><input type="checkbox" name="pakaiduedate" />TANGGAL PENYELESAIAN :
			<input class="text_area" type="text" name="mulaiberlaku" id="mulaiberlaku" size="20" maxlength="19" value="<?php //echo date("Y-m-d" ); ?>" />
			<input name="reset" type="reset" class="button" onclick="return showCalendar('mulaiberlaku', 'YYYYmmdd');" value="..." />
		</td>
	</tr>
</table>             
<input class="button" type="submit" value="KIRIM" onclick="submitbutton()" />		
</td></tr>

</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<table>
	<tr><td><a href="lihatmemo.do">KEMBALI</a></td></tr> 	
	<?php $feedback = "https://mail.google.com/mail/u/0/?view=cm&fs=1&tf=1&to=systemsm@siantarmaju.com&su=FEEDBACK+MEMO+GOOGLE+APP+ENGINE&body=saran/kritik"; ?>
	<tr><td><a href="<?php echo $feedback ?>">KRITIK DAN SARAN</a></td></tr>	
</table>