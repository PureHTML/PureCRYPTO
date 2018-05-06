<?php
/**
 * @author Simon Formanek <mail at simonformanek.cz>
 * @copyright (c) 2018, PureHTML
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace Test\PureCRYPTO;
require_once '../vendor/autoload.php';
$cryptor = new \PureCRYPTO\SslCrypto();
$cryptor->setPassphrase('password');

if ($cryptor->createKey()){
  file_put_contents('privateKey.asc', $cryptor->getPrivateKey());
  file_put_contents('publicKey.txt', $cryptor->getPublicKey());
} else {
  echo 'Error creating keys';
  exit(1);
}
