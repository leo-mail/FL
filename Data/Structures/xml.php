<?PHP
namespace FireLion\Data\Structures\XML;
require_once("array.php");
use FireLion\Data\Structures\Arr;
function ToArray( $data, $PreserveKeys=false, $ArrayIntermatch=true )
{
	$uname = __NAMESPACE__ . "\\" . __FUNCTION__;

	$err = function( $char )use($uname)
	{
		trigger_error( 
			"$uname: Unexpected " . ((strlen($char)>1)?"$char":"character \"$char\""),
			E_USER_ERROR
			);
	};
	
	$keys = $output = $been = []; $isKeyExit = false;
	$key = "";
	$keyIndex = -2;
	$clr = false;
	
	$write = function($char, $isKey, $caseKey)use(&$key, &$keys, &$keyIndex, &$output, &$been)
	{
		if( $keyIndex == -1 ) return;
		if( $isKey )
		{
			if( is_object($caseKey) && is_callable($caseKey) )
			{
				$caseKey($char);
			}else
				$key = $key.$char;
		} else {
			IF(Arr\SubLevelExists($output, $keys))
			{
				if(Arr\SubLevelGetType($output, $keys)!=="array")
				Arr\SubLevelAppend($output, $char, $keys);
			}
			else
				Arr\SubLevelSet($output, $char, $keys);
		}
	};
	
	$check_format = function()use(&$keys, &$output)
	{
		if(Arr\SubLevelGetType($output, $keys) == "array") return;
		$res = strtolower(Arr\SubLevelGet($output, $keys));
		if($res == "no" || $res == "false")
		{
			$res = false;
		} elseif( $res == "yes" || $res == "true" ) {
			$res = true;
		} elseif( is_numeric($res) )
		{
			if( substr_count($res, ",") == 1 )
			{
				$res = (float) $res;
			} else {
				$res = (int) $res;
			}
		} else return;
			Arr\SubLevelSet($output, $res, $keys);
	};

	$LastByte = strlen($data)-1;
	$brackets = $isKey = false;
	foreach(str_split($data) as $Byte=>$Char)
	{
		switch($Char)
		{
			case '"':
			case "'":
			{
				if( $brackets!==false )
				{
					if( $brackets == $Char )
					{
						$brackets = false;
					} else {
						$write($Char,$isKey,false);
					}
				} else {
					$brackets = $Char;
				}
			}	break;
			case "<":
			{
				if( $brackets )
				{
					$write($Char,$isKey,false);
				} else {
					if(!$isKey)
					{
						$isKey = true;
						$keyIndex++;
					} else
						$err($Char);
				}
			}	break;
			case ">":
			{
				if( $brackets )
				{
					$write($Char,$isKey,false);
				} else {
					if($isKeyExit)
					{
						$isKeyExit = false;
						$isKey = false;
						if($key == $clr)
						{
							array_pop($keys);
							$clr = false;
						}
						$key = "";
					} elseif( $isKey )
					{
						$isKeyExit = false;
						$isKey = false;
						
						if( trim($key) == "")
						{
							if($keyIndex>-1)
							{
								$err("Empty key");
								break 2;
							}
						} else{
							$keyss = implode("<",array_merge($keys,[$key]));
							$keys[] = $PreserveKeys? strtolower($key): $key;
							if(IsSet($been[$keyss]))
							{
								
								if($been[$keyss]==0)
								{
									Arr\SubLevelSet($output, [0=>Arr\SubLevelGet($output, $keys)], $keys);
									$_r = array_keys($been);
									$start = array_search($keyss,$_r,true);
									$strlen = strlen($keyss);
									if($start>0)
									{
										for($i=$start-1;$i>0;$i--)
										{
											if( strlen($_r[$i])>$strlen && substr( $_r[$i], 0, $strlen)==$keyss )
												unset($been[$_r[$i]]);
										}
									}
								}
								$keys[] = $been[$keyss] + 1;
								$clr = $key;
							}
							$keyIndex++;
							$key = "";
						}
					}else $write($Char,$isKey,$err);
				}
			}	break;
			case "/":
			{
				if( $brackets )
				{
					$write($Char,$isKey,false);
				} else {
					
					if($prev=="<")
					{
						if($isKey)
						{
							$isKeyExit = true;
							$check_format();
							
							$key = "";
							$keyIndex-=2;
							$keyss = implode("<",$keys);
							if(!isset($been[ $keyss ]))
							{
								$been[$keyss] = 0;
							} else $been[$keyss]++;
							array_pop($keys);
						}
					} else {
						$write($Char,$isKey,false);
					}
				}
			}	break;
			default:
			{
				if( $keyIndex > -1 )
				{
					$write($Char,$isKey,false);
				}
			}	break;
		}
		if( $Byte == $LastByte )
		{
			if( $isKey )
				$err("Unclosed key");
			if( $brackets!==false )
				$err("Unclosed quotes");
		}
		$prev = $Char;
	}
	return $output;
}

function VarToValue(&$val)
{
	$strin = function($string, $needle)
	{
		if( is_array($needle) )
		{
			foreach($needle as $needle_el)
				if(strpos($string, $needle_el)!==false) return true;
		} else
		{
			return strpos($string, $needle_el)!==false;
		}
	};
		if( is_string($val) )
		{
			$LChar = strlen($val)-1;
			if(
				$strin($val,["<", ">", "\\", "//", "#", "{", "}", "[", "]", "&", "?", "`"])
				&&	
				($val[0]!=="\""&&$val[0]!=="'")
				&&
				($val[$LChar]!=="\""&&$val[$LChar]!=="'")
			  )
			$val = "\"$val\"";
		}
		if( is_resource($val) )
			$val = "resource#" . (string)$val;
};

function Is($data, $silent=true)
{
	static $keyerrs = ["Unexpected char \"%s1\"","Unresolved or empty key","Unclosed tag - \"%s1\""];
	$uname = __NAMESPACE__ . "\\" . __FUNCTION__;
	$err = function($type,$reason)use($silent,$uname,$keyerrs)
	{
		if($silent) return;
		trigger_error( 
			"$uname - Parsing error:".PHP_EOL.str_replace("%s",$reason,$keyerrs[$type]),
			E_USER_ERROR
			);
	};
	
	$LastByte = strlen($data)-1;
	$isKey = false;
	$key = $brackets = false;
	$keyExists = -1;
	$prev = "";
	foreach($data as $Byte=>$Char)
	{
		switch($Char)
		{
			case '"':
			case "'":
			{
				if( $brackets!==false )
				{
					if( $brackets == $Char )
					{
						$brackets = false;
					} else {
						if($isKey) $key .= $Char;
					}
				} else {
					$brackets = $Char;
				}
			}	break;
			case "<":
			{
				if( $brackets )
				{
					if($isKey)
						$key .= $Char;
				} else {
					if(!$isKey)
					{
						$isKey = true;
						$keyExists++;
					} else {
						$err(0,$Char);
						return false;
					}
				}
			}	break;
			case ">":
			{
				if( !$brackets )
				{
					if( $isKey )
					{
						$isKey = false;
						$keyExists--;
						if( trim($key) == "")
						{
							$err(1,"");
							return false;
						}
						if( $prev == "/" )
							$key = "";
					}
				}
			}	break;
			default:
			{
				if( $Char !== " " && $Char !== "\t" && $Char !== "\r" && $Char !== "\n" && trim($Char)!=="" )
				{
					if( !$keyExists<0 && !$isKey  )
					{
						$err(0,$Char);
						return false;
					}
					if( $isKey )
						$key .= $Char;
				}
			}	break;
		
		}
		if( $Byte == $LastByte )
		{
			if($isKey||$isKeyExit)
			{
				$err(2,"<");
				return false;
			}
			if($brackets!==false)
			{
				$err(0,$brackets);
				return false;
			}
		}
		$prev = $Char;
	}
	return true;
}

function FromArray($data, $title="XML", $br="\t")
{
	$output = $br."<$title>" . PHP_EOL;
	if( is_array($data) )
	{
		foreach( $data as $part=>$value )
		{
			VarToValue($value);
			if( is_numeric($part) ) $part = "$title.$part";
			$br2 = str_repeat($br,2);
			$output .= (is_array($value)? FromArray($value,$part,$br2): $br2."<$part>".PHP_EOL.$br2.$value.PHP_EOL.$br2."</$part>").PHP_EOL;
			
		}
	} else {
		VarToValue($data);
		$output .= $br.$data.PHP_EOL;
	}
	$output .= $br."</$title>";
	return $output;
}