<script type="text/javascript">
function checkform(f) {
  if (f.elements["textfield"].value == f.elements["nikpembuat"].value) {
	return true;
  } else {
	if (f.elements["textfield"].value == "") {
		window.alert("Masukan NIK Pembuat");			
	} else {
	   window.alert(" Identitas tidak sama, \n Periksa apakah account anda sudah benar \n Jika ada kesulitan hubungi MIS " );
	}  		    	
   return false;			
  }
}
</script>		

<script type="text/javascript">

var filter_authorid = new Array;
<?php
$i = 0;
if (isset($images) ) {
	foreach ($images as $k=>$items) {
		foreach ($items as $v) {
			if ( isset($v->value ) && isset($v->text) ) {
				echo "filter_authorid[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
			}	
		}
	}
}	
?>

var filter_authorid2 = new Array;
<?php
$i = 0;
if (isset($images) ) {
	foreach ($images as $k=>$items) {
		foreach ($items as $v) {
			if ( isset($v->value ) && isset($v->text) ) {	
				echo "filter_authorid2[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
			}	
		}
	}
}
?>

var filter_authorid22 = new Array;
<?php
$i = 0;
if (isset($images) ) {
	foreach ($images as $k=>$items) {
		foreach ($items as $v) {
			if ( isset($v->value ) && isset($v->text) ) {
				echo "filter_authorid22[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
			}	
		}
	}
}	
?>

var filter_authorid3 = new Array;
<?php
$i = 0;
foreach ($authors3 as $k=>$items) {
	foreach ($items as $v) {
		if ( isset($v->value ) && isset($v->text) ) {
			echo "filter_authorid3[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
		}
	}
}
?>
var filter_authorid4 = new Array;
<?php
$i = 0;
foreach ($authors3 as $k=>$items) {
	foreach ($items as $v) {
		if ( isset($v->value ) && isset($v->text) ) {
			echo "filter_authorid4[".$i++."] = new Array( '$k','".addslashes( $v->value )."','".addslashes( $v->text )."' );\t";
		}	
	}
}
?>
</script>


<script type="text/javascript">

function submitbutton(pressbutton) {
	var form = eval( 'document.adminForm' );	
	
	var temp = new Array;
	for (var i=0, n=form.filter_authorid2.options.length; i < n; i++) {
		temp[i] = form.filter_authorid2.options[i].value;
	}
	form.images.value = temp.join( ',' );

	var temp2 = new Array;
	for (var i=0, n=form.filter_authorid4.options.length; i < n; i++) {
		temp2[i] = form.filter_authorid4.options[i].value;
	}
	form.authors3.value = temp2.join( ',' );


	var temp3 = new Array;
	for (var i=0, n=form.filter_authorid6.options.length; i < n; i++) {
		temp3[i] = form.filter_authorid6.options[i].value;
	}
	form.authors5.value = temp3.join( ',' );
	
	
}


function addSelectedToList( frmName, srcListName, tgtListName ) {
var form = eval( 'document.' + frmName );
var srcList = eval( 'form.' + srcListName );
var tgtList = eval( 'form.' + tgtListName );

var srcLen = srcList.length;
var tgtLen = tgtList.length;
var tgt = "x";

	for (var i=tgtLen-1; i > -1; i--) {
		tgt += "," + tgtList.options[i].value + ","
	}

	for (var i=0; i < srcLen; i++) {
		if (srcList.options[i].selected && tgt.indexOf( "," + srcList.options[i].value + "," ) == -1) {
			opt = new Option( srcList.options[i].text, srcList.options[i].value );
			tgtList.options[tgtList.length] = opt;
			tgtList.options[0].seleced = true;
		}		
		if (srcList.options[i].selected) {
			srcList.options[i] = null;
		}
	}

	var temp = new Array;
	for (var i=0, n=form.filter_authorid2.options.length; i < n; i++) {
		temp[i] = form.filter_authorid2.options[i].value;
	}
	form.images.value = temp.join( ',' );

	var temp2 = new Array;
	for (var i=0, n=form.filter_authorid4.options.length; i < n; i++) {
		temp2[i] = form.filter_authorid4.options[i].value;
	}
	form.authors3.value = temp2.join( ',' );


	var temp3 = new Array;
	for (var i=0, n=form.filter_authorid6.options.length; i < n; i++) {
		temp3[i] = form.filter_authorid6.options[i].value;
	}
	form.authors5.value = temp3.join( ',' );
}

function delSelectedFromList( frmName, srcListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( 'form.' + srcListName );

		var srcLen = srcList.length;

		for (var i=srcLen-1; i > -1; i--) {
			if (srcList.options[i].selected) {
				srcList.options[i] = null;
			}
		}
}

function moveInList( frmName, srcListName, index, to) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( 'form.' + srcListName );
		var total = srcList.options.length-1;

		if (index == -1) {
			return false;
		}
		if (to == +1 && index == total) {
			return false;
		}
		if (to == -1 && index == 0) {
			return false;
		}

		var items = new Array;
		var values = new Array;

		for (i=total; i >= 0; i--) {
			items[i] = srcList.options[i].text;
			values[i] = srcList.options[i].value;
		}
		for (i = total; i >= 0; i--) {
			if (index == i) {
				srcList.options[i + to] = new Option(items[i],values[i], 0, 1);
				srcList.options[i] = new Option(items[i+to], values[i+to]);
				i--;
			} else {
				srcList.options[i] = new Option(items[i], values[i]);
		   }
		}
		srcList.focus();
}

</script>