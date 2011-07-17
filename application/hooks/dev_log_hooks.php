<?php

// Dev log hooks class
class Dev_log_hooks
{
    // Triggers dev log to render the report summary
    function send_summary()
    {
        $CI =& get_instance();
        if(isset($CI->dev_log)) $CI->dev_log->summary_hook();
    }
}