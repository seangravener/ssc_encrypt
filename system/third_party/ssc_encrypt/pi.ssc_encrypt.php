<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name'       		=> 'SSC Encrypt',
	'pi_version'    		=> '.01',
	'pi_author'     		=> 'Sean Gravener',
	'pi_author_url' 		=> 'http://sean.gravener.net/',
	'pi_description'		=> 'Encrypt and decrypt arrays from url strings',
	'pi_usage'      		=> Ssc_encrypt::usage()
);

class Ssc_encrypt {

	var $return_data;
	
	/**
	 * Constructor
	 *
	 */
	function Ssc_encrypt()
	{
		$this->EE =& get_instance();

	}

	function encrypt () {
		
		$decrypt_key	= $this->_get_param('decrypt_key');
		$values			= $this->_get_param('values');

		// available tags
		$values = array (
			'register_key' => 'dM3xpGqexN6r',
			'fname' 		=> '',
			'lname'			=> '',
			'email' 		=> '',
			'sku' 			=> '',
			'price' 		=> '',
			'order_id' 		=> '',
			'product_id' 	=> ''
		);

		if (!$decrypt_key)
			return false;

		if (is_array($values)) {

			$string = serialize($values);
			$encrypted = strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($decrypt_key), $string, MCRYPT_MODE_CBC, md5(md5($decrypt_key)))), '+/=', '-_,');

			return $encrypted;
		}
	}

	function decrypt () {

		// set defaults
		$tags[0] = array(
			'register_key' 	=> '',
			'fname' 		=> '',
			'lname'			=> '',
			'email' 		=> '',
			'sku' 			=> '',
			'price' 		=> '',
			'order_id' 		=> '',
			'product_id' 	=> ''
		);

		// fetch params
		$encrypted 		= $this->_get_param('encrypted');
		$decrypt_key	= $this->_get_param('decrypt_key');

		if (!$decrypt_key || !$encrypted)
			return false;

		// fetch the tagdata
		$tagdata 	= $this->EE->TMPL->tagdata;

		// decrypt and unserialize the string
		$decrypted	= rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decrypt_key), base64_decode(strtr($encrypted, '-_,', '+/=')), MCRYPT_MODE_CBC, md5(md5($decrypt_key))), "\0"); 
		$values   	= unserialize($decrypted);

		if (is_array($values))
			$tags[0] = $values;
		
		// array keys will be available as variables in templates
		return $this->EE->TMPL->parse_variables($tagdata, $tags);

	}

	// --------------------------------------------------------------------
	
	function _get_param($key, $default_value = '')
	{
		$val = $this->EE->TMPL->fetch_param($key);
		
		if($val == '') {
			return $default_value;
		}
		return $val;
	}
	
	
	function usage()
	{
		ob_start(); 
		?>
		
		Initial version.

		<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file */