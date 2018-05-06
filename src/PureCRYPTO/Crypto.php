<?php

/**
 * Encrypt private data
 * 
 * @author Simon Formanek <mail at simonformanek.cz>
 * @copyright (c) 2018, PureHTML
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace PureCRYPTO;

/**
 * Description of SslCrypto
 *
 * @author Simon Formanek <mail at simonformanek.cz>
 */
abstract class Crypto {
  /**
   * @var string plaintext 
   */
	private $password = null;
  /**
   * store passphrase
   * @param string $password plaintext
   */
  public function setPassphrase($password){
    $this->password=$password;
	}

  public function getPassphrase(){
    return $this->password;
	}
}