<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Alpha.php 22697 2010-07-26 21:14:47Z alexander $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

class Z_Validate_Confirmation extends Zend_Validate_Abstract
{
    const NOT_MATCH      = 'notMatch';
 
	protected $_matchedField;
	
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH      => "Confirmation does not match"
    );

    public function __construct($fieldName)
    {
       $this->_matchedField = $fieldName;
    }

    
    public function isValid($value, $context= null)
    {
        //di($context);
        $value = (string) $value;
        $this->_setValue($value);
        
        if (is_array($context)) {
            if (isset($context[$this->_matchedField])
                && ($value == $context[$this->_matchedField])
            ) {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }
        
 
        $this->_error(self::NOT_MATCH);
        return false;
    }

}
