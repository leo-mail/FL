<?php
namespace Filabs;
require_once "../cryptography/hashes.php";
require_once "../cryptography/encryption.php";
require_once "db_funcs.php";
require_once "tokens.php";
require_once "../functions.php";
require_once "../constants.php";
require_once "../abstract.php";
class User implements VirtualAccess
{
	private $login;
	private $code;
	private $ipu;
	
	public function __construct($login)
	{
		if( !CheckStr(array_merge(range("a-Z"),range("а-Я"),[".","-","_"]),$login) )
			err(ERROR_UNCORRECT_LOGIN, $login);
		$this->login = $login;
	}
	public function register($Key,$pass)
	{
		if( db_get("uregs",$this->login) == $Key )
		{
			db_set_upass($this->login,$pass);
			db_del("uregs",$this->login);
			return Aes128Encode("auth",$pass);
		}
	}
	public function getregisterkey()
	{
		$str = StrToISum($this->generateToken) ^ mt_rand(0,288);
		db_add_ltime("uregs", $this->login, substr($str,0,8));
	}
	public function canregister($key)
	{
		if( db_get("uregs",$this->login) == $key )
			return true;
		
		return false;
	}
	public function request($method, $params, $ipu)
	{
		$check = CheckStr($method,range("a-Z"));
		$this->ipu = $ipu;
		if(!isSet($params["token"]))
		{
			if(!$check)
			{
				return $this->auth($method,true);
			} elseif( $method == "reg" )
			{
				return $this->auth($this->register($this->login,$params["key"],$params["pass"]),false,"@");
			}
		} elseif($check) {
			$this->auth($params["token"]);
			if(!$this->token)
				err(ERROR_AUTHORIZATION_FAILED);
			$this->token->activate();
			if(!$this->token->able($method))
				err(ERROR_TOKEN_UNABLED_TO_CALL_THIS_METHOD);
			$this->evaluate($method, $params);
			return;
		}
		err(ERROR_METHOD_IS_NOT_SUPPORTED);
	}
	
	protected function evaluate($method, $params)
	{
		if(!file_exists("user/{$method}.php"))
			err(ERROR_METHOD_IS_NOT_SUPPORTED);
		$this->code = file_get_contents("user/{$method}.php");
		eval($this->code);
		$this->code = "";
	}
	/*
		Auth - функция авторизации на сайте, внутренняя
		$token - токен или зашифрованная ключ-фраза "auth"
		$aes - токен или не токен
		$verify - проверять устройство?
	*/
	protected function auth($token,$aes=false,$abilities=[])
	{
		$token = false;
		if($aes && isAES($token))
		{
			if( Aes128Decode($token, db_get_upass($this->login)) == "auth" )
				$token = new Token($this->login,$this->generateToken(),$this->ipu,$abilities);
			$token->Activate();
		} elseif(!$aes)
		{
			$token = new Token($this->login,$this->ipu,$token);
		}
	}
	
	protected function generateToken()
	{
		return sha256(substr($this->login,0,mt_rand(0,strlen($this->login))).StrToISum($login).date("Dmyhis"));
	}
	
	protected function get_Token()
	{
		if( !isset($_COOKIE["token"]) ) return false;
		if( $_COOKIE["token"] == false) return false;
		return new Token($this->login,$_COOKIE["token"],$this->ipu);
	}
}