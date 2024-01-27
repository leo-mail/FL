<?
/*----------------------------------------------------------------------------\
|			FireLion Visual Framework Array Interfaces Library    |
/*----------------------------------------------------------------------------/
|									      |
|	Version: 1							      |
|	Date Modified: 27 January 2024 year				      |
|	Time:	20:20 (Ua)						      |
|	Authors:							      |
|								Лев Занченко  |
|								              |
\*----------------------------------------------------------------------------/
|
|						Types:
|				IArray		-> Normal Array
|				IDynamicArray   -> Dynamic array
|				ITypedArray	-> Array with typization
|				IAbstractArray  -> Array with abstract typization
|
*/

interface IArray extends Countable, ArrayAccess, SeekableIterator, Serializable
{
	public static $GUID = '75e8bcc4-5113-4c54-8e4f-7d9d125bbb4c';
	
	public function __construct($data);
	public function Get( $Key );
	public function Merge( $Data );
}

interface IDynamicArray extends IArray
{
	public static $GUID = '015dc5a6-a292-478f-8082-231c59571708';
	
	public function Add( $KeyValue );
	public function Remove( $Key );
	public function Replace( $Key, $NewValue );
	public function ReplaceElement( $Value, $NewValue );
	public function RemoveElement( $Value );
	public function Insert( $Key, $KeyValue );
	public function Set( $Key, $KeyValue );
	public function Lock( void );
	public function UnLock( void );
	public function IsLocked( void );
	public function Clear( void );
	
	public function __get( $name );
	public function __set( $name, $value );
}

interface ITypedArray extends IDynamicArray
{
	public static $GUID = '63777efe-0147-427a-9002-38b50e9e337d';
	
	public function GetType( void );
	public function GetTypes();
	public function AddType( $type );
	public function RemType( $type );
	public function istype( $Value );
}

interface IAbstractArray extends ITypedArray
{
	public static $GUID = '68775403-b1d3-4247-9a48-d80e57537845';
	
	public function SetType( $AType );
	public function RegisterTypeConverter( callable $AFunc, $AType, $AClass=null );
	public function GetAs($Key, $AType);
	public function GetEitherAs($Key, $Types);
	public function GetEitherNotAs($Key, $Types, $NotAs, $Default=null);
	public function GetOrNot($Key, $Not, $Default=null);
	public function SetTypes( $ATypes );
	public function LockTypes();
	public function UnLockTypes();
	public function IsTypesLocked();
}
