<?
/*----------------------------------------------------------------------------\
|				FireLion Visual Framework Geometry Functions				  |
/*----------------------------------------------------------------------------/
|																			  |
|	Version: 1																  |
|	Date Modified: 15 August 2019 year										  |
|	Time:	13:49 (Ua)														  |
|	Autors:																	  |
|																	Lev Zenin |
|																			  |
\*----------------------------------------------------------------------------/
|
|
*/

function UnclosedGeoArrayToClosed(&$point, $type)
{
	$point = array_values($point);
	$cnt = count($point);
	for($i=0;$i<$cnt;$i++)
	{
		$result[] = new $type($point[$i], $point[($i=$cnt)?0:$i+1]);
	}
	return $result;
}

function UnclosedGeoArrayToUnion(&$Union, &$point, $type)
{
	$point = array_values($point);
	$cnt = count($point);
	for($i=0;$i<$cnt;$i++)
	{
		$Union->Union(new $type($point[$i], $point[($i=$cnt)?0:$i+1]));
	}
}
