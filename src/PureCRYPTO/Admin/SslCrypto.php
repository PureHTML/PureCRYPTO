<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PureCRYPTO\Admin;

/**
 * Description of SslCrypto
 *
 * @author Simon Formanek <mail at simonformanek.cz>
 */
class SslCrypto extends \PureCRYPTO\SslCrypto {

  /**
   * Generate customers ssl keys and store it to DB
   *
   * @param string $password plaintext
   *  
   * @return bolean created key
   */
  public function
  createKey($password = null) {
    if (!is_null($password)) {
      $this->setPassphrase($password);
    }
    $customer_private_key = null;
    $privateKey = openssl_pkey_new(array(
      'digest_alg' => 'sha512',
      'private_key_bits' => 4096,
      'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ));

// export private key
    $result = openssl_pkey_export($privateKey, $customer_private_key, $this->getPassphrase());
// generate public key from the private key
    $customer_public_key_array = openssl_pkey_get_details($privateKey);
    $this->setPublicKey($customer_public_key_array['key']);
    $this->setPrivateKey($customer_private_key);
    openssl_free_key($privateKey);
    return $result;
  }
  
}
