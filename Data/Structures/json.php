<?PHP
namespace FireLion\Data\Structures\Json;
include("xml.php");
use FireLion\Data\Structures\XML;
function is( $string, $silent=true, $jsv=2 )
{
	static $keyerrs = ["Unexpected char \"%s1\"","Unresolved key - \":\"","Unclosed tag - \"%s1\""];
	$uname = __NAMESPACE__ . "\\" . __FUNCTION__;
	$err = function($type,$reason)use($silent,$uname,$keyerrs)
	{
		if($silent) return;
		trigger_error( 
			"$uname - Parsing error:".PHP_EOL.str_replace("%s",$reason,$keyerrs[$type]),
			E_USER_ERROR
			);
	};
	$string = str_split(str_replace(["\t", "\w", "\r", "\n", " "], "", $string));
	if(!($string[0] == "{" && ($string[1]=="{"||$string[1]=="\"") && $string[ count($string) - 1]=="}")) return false;
	$opentag =-1;
	$prev = "";
	$brackets = false;
	foreach( $string as $char )
	{
		switch($char)
		{
			case "{":
				{
					if($brackets===false)
					$opentag++;
				}
			case "}":
				{
					if($brackets===false)
					$opentag--;
					if($prev == "," && $jsv<2)
					{
							$err(0, ",");
						return false;
					} elseif($prev == ":") {
							$err(1, "");
						return false;
					}
				}
			case "\"":
			case "'":
			{
				if($brackets===false)
				{
					$brackets = $char;
				} elseif($brackets===$char) {
					$brackets = false;
				}
			}
			default:
			{
				if($prev==":")
				{
						$err(1, "");
					return false;
				}
			}
		}
		$prev = $char;
	}
	if($opentag>0)
	{
		$err(2, "array");
		return false;
	}
	if($prev=="\"" || $prev=="\'")
		{
			$err(0, $prev);
			return false;
		}
	return true;
}
function toXML( $data, $title="XML", $br="\t" )
{
	return XML\FromArray(is($data)? json_decode($data, true): $data);
}
function FromXML( $data )
{
	return json_encode(XML\ToArray(is($data)? json_decode($data, true): $data));
}
function ToArray( $data )
{
	if( is( $data ) )
		return json_decode($data,true);
	
	if( is_array($data) )
		return $data;
	
		trigger_error(__NAMESPACE__ . "\\" . __FUNCTION__ . ": Parsed data is not json string!");
	return [];
}
function FromArray( $data )
{
	return json_encode($data);
}