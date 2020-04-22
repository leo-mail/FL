<?
class Type implements ITypeConverter
{
	public $compats;
	protected $v;
	protected $t;
	public function __construct($compats, ...$v)
	{
		$this->compats = $compats;
		$this->t = $v[0];
		$this->v = $v[1];
	}
	
	public function Check($v)
	{
		return {$this->t}($v);
	}
	
	public function Compatible($type)
	{
		return isset($this->compats[$type]);
	}
	
	public function Convert($v,$type)
	{
		return {$this->v}($v,$type);
	}
}
class TypeHelperClass
{
public static $BinaryFormats = [
			"exe", "dll", "so", "bpl", "com", "cmd", "sys", "sh", "bat", "apk", "lpk", "bin", "dmp",
			"mf4", "res", "dlf", "pld", "oct", "pib", "bdf", "00", "sbn", "vtz", "sea", "ami", "fcm",
			"qq", "dpl", "pbo", "dmt", "lsb", "aab", "vdb", "dcp", "pbt", "shk", "1", "dde", "aks",
			"264", "zbf", "buml", "oca", "baml", "mdf", "x3db", "dmg", "blg", "dcu", "bny", "rbf",
			"dsm", "bxy", "bfy", "odb", "grib", "grub", "exp", "iso", "vmd", "bif", "tpa", "bws"
			];
	public static $Typos
	[
		"array"=>new Type([],[],"is_array"),
		"binaries"=>new Type([],[],
		function($file)
		{
			if(is_file($file))
				return in_array(array_pop( explode(".",$file) ), TypeHelperClass::$BinaryFormats);
			return false;
		}),
		"bin"=>function($file)
		{
			if(is_file($file))
				return in_array(array_pop( explode(".",$file) ), TypeHelperClass::$BinaryFormats);
			return false;
		},
		"bool"=>"is_bool",
		"boolean"=>"is_bool",
		"class"=>function($class)
		{
			return class_exists($class,false);
		},
		"closure"=>function($obj)
		{
			return is_object($obj) && get_class($obj)=="Closure";
		},
		"invokable"=>function($obj)
		{
			return is_object($obj) && is_callable($obj);
		},
		"function"=>function($f)
		{
			return is_callable($f) && (is_object($f) || (is_string($f) && strpos($f, '::')==false) );
		},
		"method"=>function($f)
		{
			return is_callable($f) && ((is_string($f) && strpos($f, '::')!==false) );
		},
		"dir"=>"is_dir",
		"directory"=>"is_dir",
		"path"=>function($path)
		{
			return is_file($path) || is_dir($path);
		},
		"double"=>"is_double",
		"float"=>"is_float",
		"int"=>"is_int",
		"integer"=>"is_integer",
		"long"=>"is_long",
		"num"=>"is_numeric",
		"number"=>"is_numeric",
		"numeric"=>"is_numeric",
		"link"=>"is_link",
		"exe"=>"is_executable",
		"executable"=>"is_executable",
		"file"=>"is_file",
		"finite"=>"is_finite",
		"fin"=>"is_finite",
		"inf"=>"is_infinite",
		"infinite"=>"is_infinite",
		"infinity"=>"is_infinite",
		"nan"=>"is_nan",
		"null"=>"is_null",
		"nil"=>function($nil)
		{
			return $nil==-1;
		},
		"object"=>"is_object",
		"resource"=>"is_resource",
		"string"=>"is_string",
		"str"=>"is_string",
		"scalar"=>"is_scalar",
		"invokableclass"=>function($class)
		{
			return method_exists($class, '__invoke');
		},
		"staticclass"=>function($class)
		{
			return !method_exists($class, '__construct');
		},
		"dynamicclass"=>function($class)
		{
			return method_exists($class, '__construct');
		},
		"objectclass"=>function($class)
		{
			return method_exists($class, '__construct');
		},
		"callable"=>"is_callable",
		"mixed"=> function($v){ return true; }
	];
	public static function AddType($name, ITypeConverter $converter, $subtype=false)
	{
		$name = is_string($name)?strtolower($name):$name;
		if(!is_callable($converter) ) return false;
			if($subtype!==false)
			{
				$subtype = array_search(is_string($subtype)? strtolower($subtype): $subtype, self::$Typos);
				if($subtype===false) return False;
				self::$Typos = array_merge(array_slice(self::$Typos, $subtype), [$name=>$converter], array_slice(self::$Typos, 0, $subtype);
			}	else  {
					self::$Typos[$name] = $converter;
				}
		return true;
	}
	
	public static function RemoveType($name)
	{
		$r = false;
		$name = strtolower($name);
		if(isset(self::$Typos[$name]))
		{
			unset(self::$Typos[$name]);
			$r = true;
		}
		return $r;
	}
	
	public static function TypeExists($type)
	{
		return isset( self::$Typos[is_string($type)? strtolower($type): $type] );
	}
	
	protected static function DoCall($type, $v)
	{
		if( isset(self::$Typos[$type]) )
			return self::$Typos[$type]->Check($v);
		elseif( class_exists($type, false) )
			return is_object($v) && is_a($v, $type);
	}

	public static function CheckType($type, $v, $arg=false)
	{
		$inv = substr($type,0,1) == '!';
		$r = isset(self::$Typos[$type])? self::$Typos[$type]->Check($v): false;
		$r = ($inv)? !$r: $r;
		if($arg!==false && !$r)
			trigger_error("Wrong parameter type for {$arg}!", E_CORE_ERROR);
		return $r;
	}
	
	public static function CheckTypes($types, $v, $arg=false)
	{
		if( count($types) == 0) return True;
		$types =
			usort($types, function($a,$b)
			{
				$sa = substr($a,0,1);
				$sb = substr($b,0,1);
				if( $sa == $sb ) return 0;
				if( $sa == '!') return 1;
				if( $sb == '!') return -1;
				return 0;
			});
		foreach($types as $type)
		{
			if( substr($type,0,1) == '!')
			{
				if( self::DoCall(substr($type,1), $v) )
				{
					if($arg!==false)
						trigger_error("Parameter {$arg} must not be a type of {$type}!", E_CORE_ERROR);
					return false;
				}
			} elseif( self::DoCall($type, $v) )
				return true;
		}
		if($arg!==false)
			trigger_error("Wrong parameter type for {$arg}!", E_CORE_ERROR);
		return false;
	}
	protected static function FixNot(&$v, &$t, &$not, &$def)
	{
		if( is_object($v)?!in_array(gettype($v),$not):!in_array(gettype($v),$not) ) return true;
		foreach( $not as $NT )
		{
			switch($NT)
			{
				case 'long':
				case 'int':
				case 'integer':
				$v = in_array('float', $t)? (float)$v: (in_array('double',$t)?(double)$v:(in_array('string',$t)?(string)$v:$def));
				
				case 'float':
				$v = in_array('double',$t)?(double)$v: ((in_array('int', $t)||in_array('integer', $t)||in_array('long', $t))?
				 (integer)$v:(in_array('string',$t)?(string)$v:$def));
				
				case 'double':
				$v = in_array('float',$t)?(float)$v: ((in_array('int', $t)||in_array('integer', $t)||in_array('long', $t))?
				 (integer)$v:(in_array('string',$t)?(string)$v:$def));
				
				case 'bigint':
				$v = (in_array('int', $t)||in_array('integer', $t)||in_array('long', $t))
				? PHP_INT_MAX: (in_array('string',$t)?(string)$v:$def);
				
				case 'bool':
				case 'boolean':
				$v = (in_array('int', $t)||in_array('integer', $t)||in_array('long', $t))?
				 (integer)$v:(in_array('float',$t)?(float)$v: (in_array('string',$t)?(string)$v:$def));
				
				default:
				{
					$v = $def;
					return false;
				} break;
			}
		}
		return true;
	}
	public static function ConvertValue(&$v,$c=false,&$arr,&$Default=null)
	{
		if(!$c)
			$c =& self::$Typos;
		$r = $Default;
		$types = array_filter($arr, function($v){return substr($v,0,1)!=='!';});
		$not = array_filter($arr, function($v){return substr($v,0,1)==='!';});
		foreach ($types as $T)
		{
			$vT = self::GetType($vT);
			if( isset($c[$vT]) && $c[$vT]->compatible($T) )
				{
					$r = $c[$vT]->convert($v,$T);
				} elseif (is_object($v))
				{
					$class = get_class($v);
					if(isset($c[$class]) && $c[$class]->compatible($T))
					{
						$r = $c[$class]->convert($v,$T);
					}
				} else {
					$v = $r;
					return false;
				}
		}		
		return self::FixNot($r, $types, $not, $Default);
	}
	
	public static function GetType($v)
	{
		foreach( self::$Typos as $type=>$check )
				if( $check->Check($v) ) return $type;
			return is_object($v)? get_class($v): null;
	}
	
	public static function IsType(...$args)
	{
		return (count($args==2))? self::CheckType($args[0], $args[1]): self::TypeExists($args[0]);
	}
	
	public static SetType($v, $type)
	{
		$type = is_string($type)? strtolower($type): $type;
		if($type == "object" && is_object($v)) return True;
		if( !IsSet(self::$Typos[$type]) )
		{
			trigger_error("Type \"".print_r($type,true)."\" not found!", E_CORE_ERROR);
			RETURN NULL;
		}
		return self::$Typos[$type]->Convert($v, self::GetType($v));
	}
	
}

if( extension_loaded("typization") || _FL_LOADED_ )
{
	Typization_register(["TypeHelperClass::gettype", "TypeHelperClass::settype"]);
} elseif ( extension_loaded("runkit") )
{
	foreach([["gettype",'$var'], ["settype", '$var, $type']] as $i)
		runkit_function_redefine ( $i[0], $i[1], "TypeHelperClass::{$i[0]}({$i[1]});"); 
}

function AddType($name, $check_func, $convert_func)
{
	return TypeHelperClass::AddType($name, $check_func, $convert_func);
}

function RemoveType($name)
{
	return TypeHelperClass::RemoveType($name);
}

function CheckType($type, $var, $Arg=false)
{
	if( is_array($type) )
		return TypeHelperClass::CheckTypes($type, $var, $Arg);
	return TypeHelperClass::CheckType($type, $var, $Arg);
}

function CheckTypes($types, $var, $Arg=false)
{
	return TypeHelperClass::CheckTypes($types, $var, $Arg);
}

function IsType(...$args)
{
	return TypeHelperClass::isType(...$args);
}