<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_DB_mysql_driver
 *
 * DB extension class to give various parsing
 * methods.  Generally speaking, these are
 * standard methods for converting a DB query
 * into an array/result.
 *
 * Modified by Shawn McCool (heybigname.com)
 * to enable custom dev log library.
 *
 * @author Simon Emms <simon@simonemms.com>
 * @author Shawn McCool <shawn@heybigname.com>
 */
class MY_DB_mysql_driver extends CI_DB_mysql_driver
{
    var $CI = false;

    final public function __construct($params)
    {
        parent::__construct($params);
        if(!$this->CI) $this->CI =& get_instance();

        log_message('debug', 'Extended DB driver class instantiated!');
    }

    public function _execute($sql)
    {
        // We know where we're not wanted...
        if(!$this->CI->dev_log->is_active())
            return parent::_execute($sql);

        // Hook
        $this->CI->dev_log->pre_query_hook($sql);

        // Execute
        $result = parent::_execute($sql);

        // Hook
        $this->CI->dev_log->post_query_hook($result);

        // Return
        return $result;
    }
}