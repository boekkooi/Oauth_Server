<?php
namespace Tests\Signature;
 /**
 * 
 *
 * @category  PEAR2
 * @package   PEAR2_Pyrus
 * @author    Warnar Boekkooi
 * @copyright 2010 The PEAR Group
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      http://svn.php.net/viewvc/pear2/Pyrus/
 */
class Utility extends \OAuth\Server\Signature\Utility {
    public function getSignatureInfo($method) {
        return parent::getSignatureInfo($method);
    }
}
