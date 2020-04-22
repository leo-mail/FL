<?
require_once "../constants.php";
require_once "../functions.php";
require_once "../mail.php";
require_once "db_funcs.php";
class Token
{
	const TK_EXPIRYDATE = 0;
	const TK_ABILITIES = 1;
	const TK_GEOLOCATION = 2;
	const TK_LAST_ACCESS_TIME = 3;
	
	const TKS_PENDING = 0;
	const TKS_ACTIVE = 1;
	private $login;
	private $string;
	public function Activate($token,$code)
	{
		if(!$this->check(false)) return;
		if(db_get2($this->login,"tokens",$token)==false) err(ERR_COULD_NOT_GET_TOKEN, $token);
			$data = json_decode($data,true);
			$data[ TK_GEOLOCATION ] = json_decode(base64_decode($code),true);
		db_set2($this->login,"tokens",$token,json_encode($data));
	}
	public function ProLong()
	{
		setCookie("token", $this->string, time() + TOKEN_LIFETIME, "/api/", "fi-labs.net", true);
		if($data=db_get2($this->login,"tokens",$this->string))
			$data = json_decode($data,true);
		$data[ self::TK_EXPIRYDATE ] = date_add( date_create(), new DateInterval("D".TOKEN_LIFETIME_D))->format(DTF);
		$data[ self::TK_GEOLOCATION ] = $this->useragent;
		$data[ self::TK_LAST_ACCESS_TIME ] = date_create()->format(DTF);
		db_set2($this->login,"tokens",$this->string,json_encode($data));
	}
	
	public function Able($ability)
	{
		$abilities = $this->getA();
		if(	$abilities == [] ) return false;
		if( $abilities == "@") return true;
		return in_array($ability,explode(",",$abilities));
	}
	
	private function setA($ability)
	{
		if($data=db_get2($this->login,"tokens",$this->string))
			$data = json_decode($data,true);
		$data[ self::TK_ABILITIES ] = $ability;
		db_set2($this->login,"tokens",$this->string,json_encode($data));
	}
	
	private function getA()
	{
		if($data=db_get2($this->login,"tokens",$this->string))
			return json_decode($data,true)[ self::TK_ABILITIES ];
		err(ERR_COULD_NOT_GET_TOKEN, $this->string);
	}
	
	public function expired()
	{
		if( $a = db_get2($this->login,"tokens",$this->string) )
		{
			$a = json_decode($a);
			if(( date_diff( date_create(), date_create_from_format(DTF, $a[self::TK_LAST_ACCESS_TIME]) ) >= new DateInterval("D1")) 
				and ($a[ self::TK_STATUS ] == self::TKS_PENDING))
				return true;
			if( date_create() <= date_create_from_format(DTF, $a[self::TK_EXPIRYDATE]) )
				return false;
		}
		return true;
	}
	
	public function check($c=true)
	{
		if( $a = db_get2($this->login,"tokens",$this->string) )
		{
			$a = json_decode($a,true);
			$ip = explode(".", $a[ self::TK_GEOLOCATION ][0]);
			$currentip = explode(".", $this->ipuseragent[0]);
				if($ip[0] !== $currentip[0] or $ip[1] !== $currentip[1] or $a[ self::TK_GEOLOCATION ][1] !== $this->ipuseragent[1])
				{
					if($c) //анти-спам
						$this->SendWarning();
					return false;
				}
		}
		return true;
	}
	
	public function SendWarning()
	{
		mail_to(NOREPLY_E_ADDRESS, $this->login, "Security Alert",
		str_replace(["%ip%","%ua%","%link%",
					[$this->ipuseragent[0],
					$this->ipuseragent[1],
					"https://fi-labs.net/api/activate.php?a=".base64_decode(json_decode($this->ipuseragent))."&token=".$this->string]);
	}
	
	public function getExpiryDate()
	{
		if($data=db_get2($this->login,"tokens",$this->string))
		{
			$data = json_decode($data,true);
			return $data[ self::TK_EXPIRYDATE ];
		}
		return date_create()->format(DTF); //вообще происходить не должно, но в жизни всякое бывает
	}
	
	public function __construct($user, $token, $ipuseragent, $abilities=[])
	{
		$this->login = $user;
		$this->string = $token;
		$this->ipuseragent = $ipuseragent;
		if(empty($abilities))
		{
			if(!$this->expired())
			{
				$this->ProLong();
				if($this->check())
					$this->Activate();
			} else 
			{
				db_delete2($this->login,"tokens",$this->string);
				err(ERR_TOKEN_EXPIRED, $this->string);
			}
		} else {
			$this->setA($abilities);
			$data=db_get2($this->login,"tokens",$this->string)
			$data[ TK_GEOLOCATION ] = $ipuseragent;
			db_set2($this->login,"tokens",$this->string,json_encode($data));
			$this->ProLong();
		}
	}
}