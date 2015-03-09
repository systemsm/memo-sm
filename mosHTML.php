<?
class mosHTML {
	public static function makeOption( $value, $text='', $value_name='value', $text_name='text' ) {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim( $text ) ? $text : $value;
		return $obj;
	}

  function writableCell( $folder, $relative=1, $text='', $visible=1 ) {
	$writeable 		= '<b><font color="green">Writeable</font></b>';
	$unwriteable 	= '<b><font color="red">Unwriteable</font></b>';

  	echo '<tr>';
  	echo '<td class="item">';
	echo $text;
	if ( $visible ) {
		echo $folder . '/';
	}
	echo '</td>';
  	echo '<td align="left">';
	if ( $relative ) {
		echo is_writable( "../$folder" ) 	? $writeable : $unwriteable;
	} else {
		echo is_writable( "$folder" ) 		? $writeable : $unwriteable;
	}
	echo '</td>';
  	echo '</tr>';
  }
	public static function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {	
		// check if array
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		$html 	= "\n<select name=\"$tag_name\" $tag_attribs>";
		$count 	= count( $arr );

		for ($i=0, $n=$count; $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " selected=\"selected\"" : '');
			}
			$html .= "\n\t<option value=\"".$k."\"$extra>" . $t . "</option>";
		}
		$html .= "\n</select>\n";

		return $html;
	}
	public static function radioList( &$arr, $tag_name, $tag_attribs, $selected=null, $key='value', $text='text' ) {
		reset( $arr );
		$html = "";
		for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
			$k = $arr[$i]->$key;
			$t = $arr[$i]->$text;
			$id = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected )) {
				foreach ($selected as $obj) {
					$k2 = $obj->$key;
					if ($k == $k2) {
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ($k == $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"radio\" name=\"$tag_name\" id=\"$tag_name$k\" value=\"".$k."\"$extra $tag_attribs />";
			$html .= "\n\t<label for=\"$tag_name$k\">$t</label>";
		}
		$html .= "\n";

		return $html;
	}
	
	
	function sortIcon( $base_href, $field, $state='none' ) {
		global $mosConfig_live_site;

		$alts = array(
			'none' 	=> _CMN_SORT_NONE,
			'asc' 	=> _CMN_SORT_ASC,
			'desc' 	=> _CMN_SORT_DESC,
		);
		$next_state = 'asc';
		if ($state == 'asc') {
			$next_state = 'desc';
		} else if ($state == 'desc') {
			$next_state = 'none';
		}

		$html = "<a href=\"$base_href&field=$field&order=$next_state\">"
		. "<img src=\"$mosConfig_live_site/images/M_images/sort_$state.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"{$alts[$next_state]}\" />"
		. "</a>";
		return $html;
	}

	/**
	* Writes Close Button
	*/
	function CloseButton ( &$params, $hide_js=NULL ) {
		// displays close button in Pop-up window
		if ( $params->get( 'popup' ) && !$hide_js ) {
			?>
			<script language="javascript" type="text/javascript">
			<!--
			document.write('<div align="center" style="margin-top: 30px; margin-bottom: 30px;">');
			document.write('<a href="#" onclick="javascript:window.close();"><span class="small"><?php echo _PROMPT_CLOSE;?></span></a>');
			document.write('</div>');
			//-->
			</script>
			<?php
		}
	}

	/**
	* Writes Back Button
	*/
	function BackButton ( &$params, $hide_js=NULL ) {
		// Back Button
		if ( $params->get( 'back_button' ) && !$params->get( 'popup' ) && !$hide_js) {
			?>
			<div class="back_button">
				<a href='javascript:history.go(-1)'>
					<?php echo _BACK; ?></a>
			</div>
			<?php
		}
	}

	/**
	* Cleans text of all formating and scripting code
	*/
	function cleanText ( &$text ) {
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = strip_tags( $text );
		$text = htmlspecialchars( $text );

		return $text;
	}

	function encoding_converter( $text ) {
		// replace vowels with character encoding
		$text 	= str_replace( 'a', '&#97;', $text );
		$text 	= str_replace( 'e', '&#101;', $text );
		$text 	= str_replace( 'i', '&#105;', $text );
		$text 	= str_replace( 'o', '&#111;', $text );
		$text	= str_replace( 'u', '&#117;', $text );

		return $text;
	}
}

class passwordgenerator
{
	var $return_type;	//stores the return type - string integer or mixed
	var $group_char;	//stores all the alphabethe in lower and upper case
	var $groun_num;		// stores the number used
	var $return_count; // stores the number of strings you expect from the class
	var $str_error;		// not used at the moment- it will store errors
	var $strmix;       // mixed varibles

	public function passwordgenerator()
	{
		$this->group_num=array(0,1,2,3,4,5,6,7,8,9);
		$this->group_char=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			$this->strmix=array('a','b','0','c','d','2','e','f','g','1','h','i','j','6','k','l','m','7','n','o','p','5','q','r','s','t','u','v','w','x','y','z','A','B','C','8','D','E','F','G','H','I','J','K','L','9','M','N','O','P','Q','R','4','S','T','U','V','W','X','3','Y','Z');
	}
	//@param $str_return as String - Expecting int/interger/str/string/mix/mixed
	//@param $str_count as Integer/Numeric
	function genPassword($str_return='mix',$str_count=10)
	{
			$this->return_count=$str_count;

		switch(strtolower($str_return))
		{
			case "string":
			case "str":
				return $this->getStrVal();
			break;
			case "integer":
			case "int":
				return $this->getIntVal();
			break;
			case "mix":
			case "mixed":
				return $this->getMixVal();
			break;
			default:
				return $this->getStrVal();
			break;
		}
	}
	function getRandNum($var_count)
	{
		srand ((float)microtime()*1000000);
		return round(rand(0,$var_count));
	}
	function getMixVal()
	{
		$var_dump='';
		for($my_x=0;$my_x<$this->return_count;$my_x++)
		{
			if($var_dump=='')
			{
				$dump_data=$this->strmix[$this->getRandNum(count($this->strmix)-1)];
				$var_dump="$var_dump$dump_data";
			}
			else
			{
				$dump_data=$this->strmix[$this->getRandNum(count($this->strmix)-1)];
				$var_dump="$var_dump$dump_data";
			}
		}
		return substr($var_dump,0,$this->return_count);
	}
	function getIntVal()
	{
		$str_dump='';
		for($my_x=0;$my_x<$this->return_count;$my_x++)
		{
			if($str_dump=='')
			{
				$dump_data=$this->getRandNum(9);
				$str_dump="$dump_data";
			}
			else
			{
				$dump_data=$this->getRandNum(9);
				$str_dump="$str_dump$dump_data";
			}
		}
		return substr($str_dump,0,$this->return_count);
	}
	function getStrVal()
	{
		$var_dump='';
		for($my_x=0;$my_x<$this->return_count;$my_x++)
		{
			if($var_dump=='')
			{
				$dump_data=$this->group_char[$this->getRandNum(count($this->group_char)-1)];
				$var_dump="$var_dump$dump_data";
			}
			else
			{
				$dump_data=$this->group_char[$this->getRandNum(count($this->group_char)-1)];
				$var_dump="$var_dump$dump_data";
			}
		}
		return substr($var_dump,0,$this->return_count);
	}
	function getError()
	{
		return $this->str_error;
	}
}
$DOZPASSGEN=new passwordgenerator();
?>