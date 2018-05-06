<?php

/**
 * @author Simon Formanek <mail at simonformanek.cz>
 * @copyright (c) 2018, PureHTML
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace PureCRYPTO;

interface PureCryptor {

  public function encrypt($plaintext);

  public function decrypt($crypttext);

  public function setPassphrase($password);
}
