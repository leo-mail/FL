<?PHP
namespace FireLion\Data\Structures\Arr;
function SetUpArgs($args, &$keys, &$EndIndex, &$keyLevel, &$StartIndex)
{
	$countArgs = count($args);

	if( $countArgs >= 3)
	{
		list($keys, $EndIndex, $keyLevel) = $args;
	} elseif( $countArgs == 2 )
	{
		$keys = explode($args[1], $args[0]);
		$EndIndex = count($keys)-1;
		$keyLevel = $EndIndex;
	} else {
		$keys = $args[0];
		$EndIndex = count($keys)-1;
		$keyLevel = $EndIndex;
	}
	$StartIndex = $EndIndex - $keyLevel;
}
function SubLevelSet(array &$array, $DATA, ...$args)
/*
	SubLevelSet
	Find SubLevel'ed element by
				array &$array, $DATA,
									 $keys, $EndIndex, $keyLevel
										-> stepping around keys array
									 $path, $delimiter
										-> walking through written path
									 $keypath
										-> stepping around fixed key array
		And set it to $DATA
*/
{
	$InPut =& $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!is_array($InPut)) $InPut = [];
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	$InPut = $DATA;
}

function SubLevelExecute(array &$array, $Action, ...$args)
/*
	SubLevelSet
	Find SubLevel'ed element by
				array &$array, $DATA,
									 $keys, $EndIndex, $keyLevel
										-> stepping around keys array
									 $path, $delimiter
										-> walking through written path
									 $keypath
										-> stepping around fixed key array
		And set it to $DATA
*/
{
	$InPut =& $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!is_array($InPut)) $InPut = [];
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	$Action($InPut);
}
function SubLevelSubtract(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p -= $a;}, ...$args);
}

function SubLevelAppend(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p .= $a;}, ...$args);
}

function SubLevelPrepend(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $a.$p;}, ...$args);
}

function SubLevelAdd(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p += $a;}, ...$args);
}

function SubLevelMultiply(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p *= $a;}, ...$args);
}

function SubLevelExpat(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p **= $a;}, ...$args);
}

function SubLevelDiv(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p /= $a;}, ...$args);
}

function SubLevelPercent(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p % $a;}, ...$args);
}

function SubLevelBitAnd(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p &= $a;}, ...$args);
}

function SubLevelInc(array &$array, ...$args)
{
	SubLevelExecute($array, function(&$p){$p++;}, ...$args);
}

function SubLevelOr(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p |= $a;}, ...$args);
}

function SubLevelBitwise(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p ^ $a;}, ...$args);
}

function SubLevelDec(array &$array, ...$args)
{
	SubLevelExecute($array, function(&$p){$p--;}, ...$args);
}

function SubLevelAssignRight(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p >>= $a;}, ...$args);
}

function SubLevelAssignLeft(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p <<= $a;}, ...$args);
}

function SubLevelDefault(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){if($p===NULL) $p = $a;}, ...$args);
}

function SubLevelAssignAnd(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p && $a;}, ...$args);
}

function SubLevelAssignOr(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p || $a;}, ...$args);
}

function SubLevelAssignXor(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p xor $a;}, ...$args);
}

function SubLevelAssignClone(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = clone $a;}, ...$args);
}

function SubLevelAssignNew(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = new $a;}, ...$args);
}

function SubLevelAssignReverted(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p =! $a;}, ...$args);
}

function SubLevelBitwiseNot(array &$array, $a, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = $p|~$a;}, ...$args);
}

function SubLevelBitwiseAssignNot(array &$array, ...$args)
{
	SubLevelExecute($array, function(&$p)use($a){$p = ~$p;}, ...$args);
}

function SubLevelExists(array &$array, ...$args)
{
/*
	setSubLevel
	Find SubLevel'ed element by
				array &$array, $DATA,
									 $keys, $EndIndex, $keyLevel
										-> stepping around keys array
									 $path, $delimiter
										-> walking through written path
									 $keypath
										-> stepping around fixed key array
*/
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]]))
		{
			return false;
		} else
			$InPut = $InPut[$keys[$CurrentIndex]];
	}
	return true;
}

function SubLevelIs(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]])) return false;
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	return is_a($InPut, $a);
}
function SubLevelGetType(array &$array, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]])) return false;
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	return gettype($InPut);
}
function SubLevelGet(array &$array, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]])) return NULL;
		$InPut = $InPut[$keys[$CurrentIndex]];
	}
	return $InPut;
}

function SubLevelEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut == $a;
}

function SubLevelNotEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut != $a;
}

function SubLevelStrongEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut === $a;
}

function SubLevelStrongNotEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut !== $a;
}

function SubLevelIsLesser(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut < $a;
}

function SubLevelIsFewer(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut > $a;
}

function SubLevelIsLesserOrEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut <= $a;
}

function SubLevelIsFewerOrEquals(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut >= $a;
}

function SubLevelIsLesserOrFewer(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut <> $a;
}

function SubLevelCompare(array &$array, $a, ...$args)
{
	$InPut = $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
		$InPut =& $InPut[$keys[$CurrentIndex]];
	
	return $InPut <=> $a;
}

function SubLevelIsSet(array &$array, ...$args)
/*
	Alias of SubLevelExists
*/
{
	return SubLevelExists($array, ...$args);
}

function SubLevelUnSet(array &$array, ...$args)
{
/*
	setSubLevel
	Find SubLevel'ed element by
				array &$array, $DATA,
									 $keys, $EndIndex, $keyLevel
										-> stepping around keys array
									 $path, $delimiter
										-> walking through written path
									 $keypath
										-> stepping around fixed key array
			And delete it (unset it)
*/
	$InPut =& $array;
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]])) return false;
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	if(isSet($InPut[$keys[$CurrentIndex+1]]))
	{
		unset($InPut[$keys[$CurrentIndex+1]]);
		return true;
	}
	return false;
}


function Difference(array ...$arrays)
{
	foreach( $arrays as $k=>&$v )
	{
		if($k==1) return;
		foreach($v as $key=>&$value)
		{
			if(!isset($arrays[1][$key]))
				yield $value;
			if($arrays[1][$key]!==$value)
				yield $value;
		}
	}
}

function RecursiveDifference(array ...$arrays)
{
	$c = count($arrays)-1;
	for($i=0;$i<$c;$i++)
	{
		$arrays[$i] = Difference($arrays[$i], $arrays[$i+1]);
	}
	return $arrays[$c-1];
}
function SubLevelGetLink(array &$array, ...$args)
{
	$InPut =& $array; //In other case, the code below will assign value to the input ($array) variable...
	$keys = $EndIndex = $keyLevel = $StartIndex = 0;
	SetUpArgs($args, $keys, $EndIndex, $keyLevel, $StartIndex);	
	for($CurrentIndex=$StartIndex;$CurrentIndex<=$EndIndex;$CurrentIndex++)
	{
		if(!isSet($InPut[$keys[$CurrentIndex]]) || !is_array($InPut[$keys[$CurrentIndex]])) return NULL;
		$InPut =& $InPut[$keys[$CurrentIndex]];
	}
	return $InPut;
}
function Unique(array $array, $fmt=null)
{
	$cf = is_callable($fmt);
	//--------------------------------
	$keys = [];
	foreach( $array as $key=>&$value)
	{
		$v = $cf? $fmt($value): $value;
			if( !isSet($keys[$v]) )
				unset($value);
			else
				$keys[$v] = null;
	}
	return $array;
}
function ElementExecute(array &$array, $f = null)
{
	$InPut =& $array;
	$levels = [];
	$levelpos = [];
	$pt	   =& $array;
	$LevelDown = function(&$relative, $level) use(&$levels,&$pt)
	{
		$levels[] = $level;
		$pt =& $relative[$level];
	};
	$LevelUp = function(&$absolute) use(&$levels)
	{
		array_pop($levels);
		if(count($levels)>0)
		{
			$pt =& SubLevelGetLink($absolute, $levels);
		} else $pt =& $InPut;
	};
	$GetLevel = function() use(&$levels)
	{
		return count($levels)-1;
	};
	
	if(!is_callable($func))
	{
		return [];
	}
	
	Iteration:
	$levelstring = implode("@#@", $levels);
	if(!isSet($levelpos[$levelstring]))
		$levelpos[$levelstring] = 0;
	$i =& $levelpos[$levelstring];
	$count = count($pt);
	
	for(;$i<$count;$i++)
	{
		if( is_array($pt[$i]) )
		{
			$LevelDown($pt, $i);
			goto Iteration;
		} else {
			$pt[$i] = call_user_func($f, [$pt[$i],$levels]);
		}
	}
	if($GetLevel()>=0)
	{
		$LevelUp($InPut);
		goto Iteration;
	}
}