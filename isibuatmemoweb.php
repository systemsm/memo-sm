<?php
date_default_timezone_set('Asia/Bangkok');
include_once 'mosHTML.php';

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

$useremail = $user->getNickname();


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
<form action="kirimmemo.do" method="post" name="adminForm" id="adminForm" onsubmit="return checkform(this);"  >
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td width="40%" valign="top">
<table class="adminform">
<tr>	
<td colspan="2">					
<table width="100%">						
<tr>							
<td width="48%" valign="top">
<div align="center">
<FONT SIZE="2">KEPADA :</FONT>
<br />
<?php 
echo $lists['filter_authorid']; 
?>
</div>
</td>
<td width="2%">
<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','filter_authorid','filter_authorid2')" title="Add"/>
<br/>
<input class="button" type="button" value="<<" onclick="addSelectedToList('adminForm','filter_authorid2','filter_authorid')" title="Remove"/>
</td>
<td width="48%">
<div align="center">
<FONT SIZE="2">KEPADA:</FONT>
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
<td width="28%" valign="top">
<?php echo 'NAMA : ' ;?>
</td>
<td width="72%" valign="top">
<?php echo $loginrow['NAMA']; ?>
</td>          
</tr>
<tr>
<td width="28%" valign="top">
<?php echo 'NIK  : ';?>
</td>
<td width="72%" valign="top">
<?php echo $loginrow['NIK']; ?>
</td>          
</tr>
<tr>
<td width="28%" valign="top">
<?php echo 'BAGIAN : ';?>
</td>
<td width="72%" valign="top">
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
<td><textarea class="text_area" cols="30" rows="10" style="width: 350px; height: 250px" name="editor1" id="editor1"><?php echo ''; ?></textarea></td>
</td>
</tr>
</table>						

<table width="100%">
<tr>							
<td width="48%" valign="top">
<div align="center">
<FONT SIZE="2">MEMINTA PERSETUJUAN:</FONT>
<br />
<?php 
echo $lists['filter_authorid3'];
//echo $lists['imagefiles'];										
?>
</div>
</td>
<td width="2%">
<input class="button" type="button" value=">>" onclick="addSelectedToList('adminForm','filter_authorid3','filter_authorid4')" title="Add"/>
<br/>
<input class="button" type="button" value="<<" onclick="addSelectedToList('adminForm','filter_authorid4','filter_authorid3')" title="Remove"/>
</td>
<td width="48%">
<div align="center">
<FONT SIZE="2">MENYETUJUI:</FONT>
<br />
<?php 
//echo $lists['imagelist'];
echo $lists['filter_authorid4'];
?>
<br />
</div>
</td>

</tr>
</table>

<tr><td>
<div align="left">
<br />
<FONT SIZE="2"> VALIDASI: </FONT>
<br />
<FONT SIZE="2">Masukan NIK:</FONT><br /><input type="text" name="textfield" id="textfield" />
<input type="hidden" name="nikpembuat" id="nikpembuat"  value="<?php echo $loginrow['NIK']; ?>" />
</div>
</td></tr>						
<tr><td>

<input class="button" type="submit" value="KIRIM" onclick="submitbutton()" />		
</td></tr>

</td>
</tr>
</table>
</td>

</tr>
</table>
<input type="hidden" name="images"  id="images" />
<input type="hidden" name="authors3" id="authors3"/>
<input type="hidden" name="authors5" id="authors5" />
<input type="hidden" name="nama" value="<?php echo $loginrow['NAMA']; ?>" />
<input type="hidden" name="nik" value="<?php echo $loginrow['NIK']; ?>" />
<input type="hidden" name="bagian" value="<?php echo $loginrow['BAGIAN']; ?>" />		
<input type="hidden" name="email" value="<?php echo $loginrow['EMAIL']; ?>" />
<input type="hidden" name="option" value="<?php echo $option; ?>" />
<input type="hidden" name="task" value="" />
</form>
