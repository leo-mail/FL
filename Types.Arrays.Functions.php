<?

function array_insert(&$array, $index, $value)
{
	$array = array_merge(array_slice($array,0,$index), [$value], array_slice($array,$index));
}

function array_add(&$array, $value)
{
	$array[] = $value;
}

function array_sub(&$array, $value, $strict=false)
{
	while(array_search($value,$array,$strict)!==false)
	unset($array[ array_search($value,$array,$strict) ]);
}

function Include(&$array, $value)
{
	if( array_search($value,$array,$strict)===false )
		$array[] = $value;
}

function Exclude(&$array, $value)
{
	while(array_search($value,$array,true)!==false)
	unset($array[ array_search($value,$array,true) ]);
}


function Insert(&$array, $index, $value)
{
	$array = array_merge(array_slice($array,0,$index), [$value], array_slice($array,$index));
}

function Add(&$array, $value)
{
	$array[] = $value;
}

function Sub(&$array, $value)
{
	if( array_search($value,$array,$strict)!==false )
		unset($array[array_search($value,$array,$strict)]);	
}

//----------------------------TYPES----------------------------//

function starr(...$pieces)
{
	return new StaticArray($pieces);
}