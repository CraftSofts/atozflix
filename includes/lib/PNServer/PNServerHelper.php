<?php
namespace lib\PNServer;

/**
 * helper trait containing some methods used by multiple classes in package
 *
 * @package lib\PNServer
 * @author Stefanius <s.kien@online.de>
 */
trait PNServerHelper
{
	/**
	 * get classname without namespace
	 * @param mixed $o
	 * @return string
	 */
	public static function className($o) {
		$strName = '';
		if (is_object($o)) {
			$path = explode('\\', get_class($o));
			$strName = array_pop($path);
		}
		return $strName;
	}
	
	/**
	 * Encode data to Base64URL
	 * 
	 * @param string $data
	 * @return boolean|string
	 */
	public static function encodeBase64URL($data) {
		// First of all you should encode $data to Base64 string
		$b64 = base64_encode($data);
	
		// Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
		if ($b64 === false) {
			return false;
		}
		// Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
		$url = strtr($b64, '+/', '-_');
	
		// Remove padding character from the end of line and return the Base64URL result
		return rtrim($url, '=');
	}
	
	/**
	 * Decode data from Base64URL
	 * 
	 * @param string $data
	 * @param boolean $strict
	 * @return boolean|string
	 */
	public static function decodeBase64URL($data, $strict = false) {
		// Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
		$b64 = strtr($data, '-_', '+/');
	
		// Decode Base64 string and return the original data
		return base64_decode($b64, $strict);
	}	

	public static function getP256PEM($strPublicKey, $strPrivateKey)
	{
		$der  = self::p256PrivateKey($strPrivateKey);
		$der .= $strPublicKey;
	
		$pem = '-----BEGIN EC PRIVATE KEY-----'.PHP_EOL;
		$pem .= chunk_split(base64_encode($der), 64, PHP_EOL);
		$pem .= '-----END EC PRIVATE KEY-----'.PHP_EOL;
	
		return $pem;
	}
	
	private static function p256PrivateKey($strPrivateKey)
	{
		$key = unpack('H*', str_pad($strPrivateKey, 32, "\0", STR_PAD_LEFT))[1];
	
		return pack(
				'H*',
				'3077' 					// SEQUENCE, length 87+length($d)=32
				. '020101'				// INTEGER, 1
				. '0420'				// OCTET STRING, length($d) = 32
				. $key
				. 'a00a'				// TAGGED OBJECT #0, length 10
				. '0608'				// OID, length 8
				. '2a8648ce3d030107'	// 1.3.132.0.34 = P-256 Curve
				. 'a144' 				//  TAGGED OBJECT #1, length 68
				. '0342' 				// BIT STRING, length 66
				. '00' 					// prepend with NUL - pubkey will follow
			);
	}
	
	public static function signatureFromDER($der)
	{
		$sig = false;
		$R = false;
		$S = false;
		$hex = \unpack('H*', $der)[1];
		if ('30' === \mb_substr($hex, 0, 2, '8bit')) {
			// SEQUENCE
			if ('81' === \mb_substr($hex, 2, 2, '8bit')) {
				// LENGTH > 128
				$hex = \mb_substr($hex, 6, null, '8bit');
			} else {
				$hex = \mb_substr($hex, 4, null, '8bit');
			}
			if ('02' === \mb_substr($hex, 0, 2, '8bit')) {
				// INTEGER
				$Rl = \hexdec(\mb_substr($hex, 2, 2, '8bit'));
				$R = self::retrievePosInt(\mb_substr($hex, 4, $Rl * 2, '8bit'));
				$R = \str_pad($R, 64, '0', STR_PAD_LEFT);
			
				$hex = \mb_substr($hex, 4 + $Rl * 2, null, '8bit');
				if ('02' === \mb_substr($hex, 0, 2, '8bit')) {
					// INTEGER
					$Sl = \hexdec(\mb_substr($hex, 2, 2, '8bit'));
					$S = self::retrievePosInt(\mb_substr($hex, 4, $Sl * 2, '8bit'));
					$S = \str_pad($S, 64, '0', STR_PAD_LEFT);
				}
			}
		}
		
		if ($R !== false && $S !== false) {
			$sig = \pack('H*', $R.$S);
		}
	
		return $sig;
	}

	private static function retrievePosInt($data)
	{
		while ('00' === \mb_substr($data, 0, 2, '8bit') && \mb_substr($data, 2, 2, '8bit') > '7f') {
			$data = \mb_substr($data, 2, null, '8bit');
		}
	
		return $data;
	}

	public static function getXYFromPublicKey($strKey, &$x, &$y)
	{
		$bSucceeded = false;
		$hexData = bin2hex($strKey);
		if (mb_substr($hexData, 0, 2, '8bit') === '04') {
			$hexData = mb_substr($hexData, 2, null, '8bit');
			$dataLength = mb_strlen($hexData, '8bit');
			
			$x = hex2bin(mb_substr($hexData, 0, $dataLength / 2, '8bit'));
			$y = hex2bin(mb_substr($hexData, $dataLength / 2, null, '8bit'));
		}
		return $bSucceeded;
	}
}