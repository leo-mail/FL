<?PHP
namespace FireLion\Data\Structures\Json;
include("xml.php");
use FireLion\Data\Structures\XML;
function is( $string )
{
	$string = str_split(str_replace(["\t", "\w", "\r", "\n", " "], "", $string));
	return $string[0] == "{" && ($string[1]=="{"||$string[1]=="\"") && $string[ count($string) - 1]=="}";
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
	return FALSE;
}
function FromArray( $data )
{
	return json_encode($data);
}