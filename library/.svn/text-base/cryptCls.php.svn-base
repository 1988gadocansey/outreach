<?php
class cryptCls {
	private $algorithm;
	private $key;
	private $config;
	private $mode;
	private $stream;
	
	public function __construct(){
		$this->config = new JConfig();
		$this->algorithm = MCRYPT_RIJNDAEL_256;	
		$this->mode = MCRYPT_MODE_ECB;
		$this->Mhash();
		$this->stream = mcrypt_create_iv(mcrypt_get_iv_size($this->algorithm, $this->mode), MCRYPT_RAND);
	}//End Function
	
	public function encodeString($url){
		return(mcrypt_encrypt($this->algorithm, $this->key, $url, $this->mode, $this->stream));	
	}//End Function
	
	public function decodeString($url){
		return (rtrim(mcrypt_decrypt($this->algorithm, $this->key, $url, $this->mode, $this->stream),"\0"));
	}//End Function
	
	public function Mhash(){
		$this->key = sha1("RAC-1".$this->config->secret,TRUE);
	}//End Function
	
}//End Class
?>