<?
/*----------------------------------------------------------------------------\
|					FireLion Visual Framework Array Library		 	          |
/*----------------------------------------------------------------------------/
|																			  |
|	Version: 1																  |
|	Date Modified: 14 August 2019 year										  |
|	Time:	19:34 (Ua)														  |
|	Autors:																	  |
|														Lev Zenin Borisovitch |
|																			  |
\*----------------------------------------------------------------------------/
|
|							Types:
|				StaticArray -> Static Array object
|				DynamicArray-> Dynamic Array Object
|				TypedArray	-> Array With Typization
|				AbstractArray->Array with Abstract Typization
|
*/
require_once "Defines.php";

foreach( ["array.php", "json.php", "xml.php"] as $req=>$file )
if( file_exists(__DIR__ . $req) )
{
	$$req = true;
		require_once __DIR__ . "Data/Structures/" . $file;
} else $$req = false;
class oArray implements IArray
{
	protected $_____data;
	protected $_____count = 0;
	protected $_____position = 0;
	protected $_____locked;
	public function __construct($data)
	{
		$this->_____count = count($data);
		$this->_____data = $data;
	}
	public function Get( $Key )
	{
		return $this->_____data[$Key];
	}
	public function Merge( $data )
	{
		if( $this->_____locked ) return;
		if( is_array($data) )
		{
			foreach($data as $value)
				$this->append($value);
		} elseif ( is_object($data) ) {
			$cImple = class_implements($data, false);
			if( in_array('IAbstractArray',$cImple) )
			{
				$cnt = count($data);
				for($i=0;$i<$cnt;$i++)
					$this->append( $data->GetEitherAs($i, $this->_____types) );
			}elseif( in_array('IArray', $cImple) || in_array('ITerator', $cImple) )
				foreach($data as $v)
					$this->append($v);
		}
	}
	public function Diff($data)
	{
		if($this->_____locked) return;
		$this->_____data = array_diff($this->_____data, $data);
	}
	public function count()
	{
		return $this->_____count;
	}
	public function current()
	{
		return current($this->_____data);
	}
	public function getArrayCopy ()
	{
		return $this->_____data;
	}
	public function key ()
	{
		return key($this->_____data);
	}
	public function next ()
	{
		++$this->_____position;
		next($this->_____data);
	}
	public function prev ()
	{
		--$this->_____position;
		prev($this->_____data);
	}
	public function offsetExists ( $index )
	{
		return IsSet($this->_____data[$index]);
	}
	public function offsetGet ($index)
	{
		return $this->_____data[$index];
	}
	public function rewind ()
	{
		$this->_____position = 0;
		rewind($this->_____data);
	}
	public function seek ( $position )
	{
		if ( $position < $this->_____count )
		{
			if( $position == $this->_____position ) return;
			if( $position > $this->_____position )
			{
				while($position > $this->_____position) $this->next();
			}
			if( $position < $this->_____position )
			{
				while($position > $this->_____position) $this->prev();
			}
		}
	}
	public function serialize ()
	{
		return serialize($this->_____data);
	}
	public function unserialize ( string $serialized )
	{
		$this->_____data = unserialize($this->_____data);
	}
	public function uasort ( $cmp_function )
	{
		if($this->_____locked) return;
		uasort($this->_____data, $cmp_function);
	}
	public function uksort ( $cmp_function )
	{
		if($this->_____locked) return;
		uksort($this->_____data, $cmp_function);
	}
	public function ksort
	{
		if($this->_____locked) return;
		ksort($this->_____data);
	}
	public function natcasesort ()
	{
		if($this->_____locked) return;
		natcasesort($this->_____data);
	}
	public function natsort ()
	{
		if($this->_____locked) return;
		natsort($this->_____data);
	}
	public function asort ()
	{
		if($this->_____locked) return;
		asort($this->_____data);
	}
	public function valid (){ return $this->_____position < $this->_____count; }
	public function append ( $value )
	{
		if($this->_____locked) return;
		$this->_____data[] = $value;
		++$this->_____count;
	}
	public function offsetSet ( $index , $value )
	{
		if($this->_____locked) return;
		if(is_integer($index) && $index > $this->_____count)
			++$this->_____count;
		$this->_____data[$index] = $value;
	}
	public function offsetUnset ( $index )
	{
		if($this->_____locked) return;
		if($index > $this->_____count) return;
		if( $index >= $this->_____position )
			--$this->_____position;
		unset($this->_____data[$index]);
	}
	
	//XML
	if(${2})
		{
			public function ToXml()
			{
				return FireLion\Data\Structures\Xml\FromArray( $this->_____data );
			}
			public function FromXml($data)
			{
				$this-._____data = FireLion\Data\Structures\Xml\ToArray($data);
			}
			if( FL_ARRAY_SUBACCESS )
			{
			protected function get_Xml()
			{
				return $this->ToXml();
			}
			protected function set_Xml($data)
			{
				$this->FromXml($data);
			}
			}
		}
	//JSON
	if(${1})
		{
			public function ToJson()
			{
				return FireLion\Data\Structures\Json\FromArray( $this->_____data );
			}
			public function FromJson($data)
			{
				$this-._____data = FireLion\Data\Structures\Json\ToArray($data);
			}
			if( FL_ARRAY_SUBACCESS )
			{
			protected function get_Json()
			{
				return $this->ToJson();
			}
			protected function set_Json($data)
			{
				$this->FromJson($data);
			}
			}
		}
}

class StaticArray extends oArray
{
	public function __construct($data)
	{
		$this->_____count = count($data);
		$this->_____data = $data;
		$this->_____locked = true;
	}
}

class DynamicArray extends oArray implements IDynamicArray
{
	public function Lock()
	{
		$this->_____locked = false;
	}
	public function UnLock()
	{
		$this->_____locked = true;
	}
	public function IsLocked()
	{
		return $this->_____locked;
	}
	public function Clear()
	{
		if($this->_____locked) return;
		$this->_____data  = [];
		$this->_____position = 0;
		$this->_____count = 0;
	}
	public function Exists( $Key )
	{
		return $this->offsetExists($Key);
	}
	public function ExistsElement( $Value )
	{
		$valyes = array_flip($this->_____data);
		return isset($valyes[$Value]);
	}
	public function Add( $KeyValue )
	{
		$this->append($KeyValue);
	}
	public function Remove( $Key )
	{
		$this->offsetUnset($Key);
	}
	public function Replace( $Ley, $NewValue )
	{
		if($this->_____locked) return;
		if($this->offsetExists($Ley))
			$this->offsetSet($Ley, $NewValue);
	}
	public function ReplaceElement( $Value, $NewValue )
	{
		if($this->_____locked) return;
		$valyes = array_flip($this->_____data);
		if( isset($valyes[$Value]) )
			$this->_____data[$valyes[$Value]] = $NewValue;
	}
	public function RemoveElement( $Value )
	{
		if($this->_____locked) return;
		$valyes = array_flip($this->_____data);
		if( isset($valyes[$Value]) )
			unset($this->_____data[$valyes[$Value]]);
	}
	public function Insert( $Key, $KeyValue )
	{
		if($this->_____locked) return;
		if($this->Exists($Key))
		$this->_____data = array_merge(array_slice($this->_____data,0,$Key-1), $KeyValue, array_slice($this->_____data,$Key-1));
	}
	//------------------------------------
	public function Set( $Key, $KeyValue )
	{
		$this->offsetSet($Key, $KeyValue);
	}
	public function __get( $name )
	{
		return $this->offsetGet($name);
	}
	public function __set( $name, $value )
	{
		$this->offsetSet($name,$value);
	}
}

class TypedArray extends DynamicArray implements ITypedArray
{
	protected $_____types = [];
	protected $_____tlocked = false;
	public function __construct($type=false,$data)
	{
		if( is_string($type) )
		$this->_____types = [strtolower($type)];
		foreach($data as $i=>$v)
			$this->offsetSet($i,$v);
	}
	
	public function AddType( $type )
	{
		if($this->_____tlocked) return;
		$type = strtolower($type);
		if(!array_search($type, $this->_____types))
			$this->_____types[] = $type;
		}
	}

	public function RemoveType( $type )
	{
		if($this->_____tlocked) return;
		$types = array_flip($this->_____types);
		if( isset($types[$type]) )
			unset($this->_____types[$types[$type]]);
	}
	
	protected function CheckTypes( &$v )
	{
		if( empty( $this->_____types ) ) return true;
		return TypeHelperClass::checkTypes($this->_____types, $v);
	}
	
	public function isType($v)
	{
		return $this->CheckTypes($v);
	}
	
	public function ExistsElement( $Value )
	{
		if(!$this->CheckTypes($Value)) return;
		$valyes = array_flip($this->_____data);
		return isset($valyes[$Value]);
	}

	public function GetType()
	{
		foreach($this->_____types as $t)
			yield $t;
	}
	
	public function GetTypes( )
	{
		foreach($this->_____types as $t)
			yield	( substr($t,0,1) == '!' )? substr($t,1): $t;
	}
	
	public function Add( $LeyValue )
	{
		$this->append($LeyValue);
	}
	public function Replace( $Ley, $NewValue )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($NewValue)) return;
		if($this->offsetExists($Ley))
			$this->offsetSet($Ley, $NewValue);
	}
	public function append ( $value )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($value)) return;
		$this->_____data[] = $value;
		++$this->_____count;
	}
	public function offsetSet ( $index, $value )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($value)) return;
		if(is_integer($index) && $index > $this->_____count)
			++$this->_____count;
		$this->_____data[$index] = $value;
	}
	public function ReplaceElement( $Value, $NewValue )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($Value)
			||!$this->CheckTypes($NewValue)) return;
		$valyes = array_flip($this->_____data);
		if( isset($valyes[$Value]) )
			$this->_____data[$valyes[$Value]] = $NewValue;
	}
	public function RemoveElement( $Value )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($Value)) return;
		$valyes = array_flip($this->_____data);
		if( isset($valyes[$Value]) )
			unset($this->_____data[$valyes[$Value]]);
	}
	public function Insert( $Key, $KeyValue )
	{
		if($this->_____locked) return;
		if(!$this->CheckTypes($KeyValue)) return;
		if($this->Exists($Key))
		$this->_____data = array_merge(array_slice($this->_____data,0,$Key-1), $KeyValue, array_slice($this->_____data,$Key-1));
	}
}
class TypeLockedArray extends TypedArray
{
	public function __construct($data)
	{
		foreach($data as $i=>$v)
			$this->offsetSet($i,$v);
	}
	
	public function AddType( $type )
	{
	}

	public function RemoveType( $type )
	{
	}
}

class AbstractArray extends TypedArray implements IAbstractArray
{
	protected $_____converters;
	protected function ConvertV(&$v,$types=false,$Default=null)
	{
		if(!$types) $types = $this->_____types;
		return TypeHelperClass::ConvertValue($v,$this->_____converters,$types,$Default);
	}
	protected function CheckTypes( &$v )
	{
		if(!TypeHelperClass::checkTypes($this->_____types, $v))
			return $this->ConvertV($v);
		return True;
	}
	public function settype( $Type )
	{
		$d = $this->_____data;
		$this->_____data = [];
		$this->_____types = [];
		foreach($d as $k=>$v)
		{
			if( substr($Type,0,1) == '!' )
			{
				if (TypeHelperClass::checkType(substr($Type,0,1), $v))
					$endarr[$k] = $v;
			}	else {
				if( isset($this->_____converters[gettype($v)]) && $this->_____converters[gettype($v)]->compatible($Type) )
				{
					$this->_____data[$k] = $this->_____converters[gettype($v)]->convert($v,$Type);
					unset($d[$k]);
				} elseif (is_object($v))
				{
					$class = get_class($v);
					if(isset($this->_____converters[$class]) && $this->_____converters[$class]->compatible($Type))
					{
						$this->_____data[$k] = $this->_____converters[get_class($v)]->convert($v,$Type);
							unset($d[$k]);
					}
				}
					$this->_____types[]	= $Type;
			}
		}
		if(!empty($d))
			foreach($d as $k=>$v)
			{
				if($this->ConvertV($v))
					$this->_____data[$k] = $v;
			}
	}
	public function RegisterConverter( callable $AFunc, $AType, $AClass=null );
	public function GetAs($Key, $AType)
	{
		$V = $this->OffSetGet($Key);
		$this->ConvertV( $V, [$AType]);
		return $V;
	}
	public function GetEitherAs($Key, $Types, $Default =null)
	{
		$V = $this->OffSetGet($Key);
		$this->ConvertV( $V, $Types, $Default );
		return $V;
	}

	public function SetTypes( $ATypes )
	{
		$this->_____types = []
		$d = $this->_____data;
		$this->_____data = [];
		if(!is_array($ATypes))$ATypes = [$ATypes];
		foreach( $ATypes as $Type )
		{
			foreach($d as $k=>$v)
			{
				if( substr($Type,0,1) == '!' )
				{
				
					if( !TypeHelperClass::checkType($Type, $v) )
						unset($d[$k]);
				} else	{
						if( isset($this->_____converters[gettype($v)]) && $this->_____converters[gettype($v)]->compatible($Type) )
						{
							$this->_____data[$k] = $this->_____converters[gettype($v)]->convert($v,$Type);
							unset($d[$k]);
						} elseif (is_object($v))
						{
							$class = get_class($v);
							if(isset($this->_____converters[$class]) && $this->_____converters[$class]->compatible($Type))
							{
								$this->_____data[$k] = $this->_____converters[get_class($v)]->convert($v,$Type);
								unset($d[$k]);
							}
						}
					}
				$this->_____types[]	= $Type;
			}
		}
		if(!empty($d))
			foreach($d as $k=>$v)
			{
				if($this->ConvertV($v))
					$this->_____data[$k] = $v;
			}
	}
	public function LockTypes()
	{
		$this->_____tlocked = true;
	}
	public function UnLockTypes()
	{
		$this->_____tlocked = false;
	}
	public function IsTypesLocked()
	{
		return $this->_____tlocked;
	}
}


class IntegerArray extends TypeLockedArray
{
	protected $_____types = ['integer'];
}

class FloatArray extends TypeLockedArray/
{
	protected $_____types = ['float','double'];
}

class NumericArray extends TypeLockedArray
{
	protected $_____types = ['numeric'];
}

class ArrayArray extends TypeLockedArray
{
	protected $_____types = ['array'];
}

class BoolArray extends TypeLockedArray
{
	protected $_____types = ['bool'];
}

class_alias("BoolArray", "BooleanArray", false);

class CallableArray extends TypeLockedArray
{
	protected $_____types = ['callable'];
}

Class ObjectArray extends TypeLockedArray
{
	protected $_____types = ['object'];
}

class StringArray extends TypeLockedArray
{
	protected $_____types = ['string'];//
}
