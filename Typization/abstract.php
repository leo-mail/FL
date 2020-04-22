<?php
trait VirtualAccess
{
	public function __get($n)
	{
		if( method_exists($this, "set_{$n}") )
			return $this->{"get_{$n}"}();
		if( method_exists($this, "v_{$n}") )
			return $this->{"v_{$n}"}($n);
		return NULL;
	}
	
	public function __set($n,$d)
	{
		if( method_exists($this, "set_{$n}") )
			$this->{"get_{$n}"}();
		if( method_exists($this, "v_{$n}") )
			$this->{"v_{$n}"}($n,$d);
	}
	
	public function __isset($n)
	{
		return method_exists($this, "v_{$n}") || method_exists($this, "set_{$n}") || method_exists($this, "get_{$n}");
	}
}