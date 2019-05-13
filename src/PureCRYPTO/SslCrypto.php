<?php
/**
 * Encrypt private data using libssl
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
class SslCrypto extends Crypto implements PureCryptor
{
    /**
     *
     * @var string unlocked private key 
     */
    private $privateKey = null;

    /**
     *
     * @var string public key 
     */
    private $publicKey = null;

    /**
     * set public key
     * 
     * @param string $key
     * 
     * @return boolean key is stored
     */
    public function setPublicKey($key)
    {
        $this->publicKey = $key;
        return is_null($this->publicKey) === false;
    }

    /**
     * set private key
     * @param string $key
     */
    public function setPrivateKey($key)
    {
        $this->privateKey = $key;
    }

    /**
     * Encrypt string with customers and admins key
     *
     * @param string $plaintext plaintext
     * @throws Exception ssl encryption error
     * 
     * @return string base64 encoded ncrypted text
     */
    public function encrypt($plaintext)
    {
        $crypttext = '';
        if (!openssl_public_encrypt($plaintext, $crypttext,
                $this->getPublicKey())) {
            $err = openssl_error_string();
            if (!empty($err)) {
                throw new \Exception($err);
            }
        }
        return base64_encode($crypttext);
    }
    /**
     * Decrypt crypttext with customer key
     *

     * @param sting   $crypttext   text to decrypt
     * @param string  $crypted_password    plaintext
     * @param int    $customer_id required cutomer id
     * @param string $role customer or admin
     *
     * @return string
     */

    /**
     * encrypt session password
     * @param string $source plaintext
     */
    public function encrypt_session_password($source)
    {
        $public_key = file_get_contents(SERVER_SESSION_CUSTOMER_PUBLIC_KEY);
        openssl_public_encrypt($source, $encrypted, $public_key);
        return base64_encode($encrypted);
    }

    /**
     * decrypt
     * 
     * @param string $crypttext base 64 encoded crypttext
     * @throws Exception ssl decryption error
     * 
     * @return string
     */
    public function decrypt($crypttext)
    {
        if (strstr($this->getPrivateKey(), 'BEGIN ENCRYPTED PRIVATE KEY')) {
            $privateKey = openssl_pkey_get_private($this->getPrivateKey(),
                $this->getPassphrase());
        } else {
            $privateKey = openssl_pkey_get_private($this->getPrivateKey());
        }

        if (!openssl_private_decrypt(base64_decode($crypttext), $decrypted,
                $privateKey)) {
            $err = openssl_error_string();
            if (!empty($err)) {
                throw new \Exception($err);
            }
        }
        openssl_free_key($privateKey);
        return $decrypted;
    }

    /**
     * change customers passphrase
     * 
     * @param type $password_current
     * @param type $password_new
     * @param type $customer_id
     * 
     * @return boolean
     * 
     * @throws \Exception
     */
    public function change_passphrase_customer($password_current, $password_new,
                                               $customer_id)
    {
        $result = false;

        $keys_query = tep_db_query("SELECT private_key_customer FROM ".constant('TABLE_KEYS_CUSTOMER')." WHERE customers_id = '".$customer_id."'");
        $keys       = tep_db_fetch_array($keys_query);
        if (tep_db_num_rows($keys_query)) {

            $res = openssl_pkey_get_private($keys['private_key_customer'],
                $password_current);
            if ($res === false) {
                throw new \Exception("Loading private key failed: ".openssl_error_string());
//         $messageStack->add('account_password', "Loading private key failed: " . openssl_error_string());
            }
            if (openssl_pkey_export($res, $result, $password_new) === false) {
                throw new \Exception("Passphrase change failed: ".openssl_error_string());
//        $messageStack->add('account_password', "Passphrase change failed: " . openssl_error_string());
            } else {
                $result = true;
            }
            return $result;
        }
    }

    /**
     * Generate customers ssl keys and store it to DB
     *
     * @param string $password plaintext
     * @param int $customer_id
     */
    public function
    createKey()
    {
        $customer_private_key = null;
        $privateKey           = openssl_pkey_new(array(
            'digest_alg' => 'sha512',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ));

// export private key
        $result                    = openssl_pkey_export($privateKey,
            $customer_private_key, $this->getPassphrase());
// generate public key from the private key
        $customer_public_key_array = openssl_pkey_get_details($privateKey);
        $this->setPublicKey($customer_public_key_array['key']);
        $this->setPrivateKey($customer_private_key);
        openssl_free_key($privateKey);
        return $result;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
