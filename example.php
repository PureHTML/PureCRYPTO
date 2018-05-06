<?php

/**
 * @author Simon Formanek <mail at simonformanek.cz>
 * @copyright (c) 2018, PureHTML
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace PureCRYPTO;

require_once 'vendor/autoload.php';
$cryptor = new SslCrypto();
$cryptor->setPassphrase('password');
$cryptor->setPrivateKey(file_get_contents(__DIR__ . '/tests/privateKey.asc'));
$cryptor->setPublicKey(file_get_contents(__DIR__ . '/tests/publicKey.txt'));
$encrypted = $cryptor->encrypt('example plaintext string');
$plaintext = $cryptor->decrypt($encrypted);
echo $plaintext;
