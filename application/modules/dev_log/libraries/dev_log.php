<?php

// Dev_log ...
class Dev_log
{
    private $CI = false;

    // Temporary state variables
    private $query_sql;
    private $query_start_time;
    private $query_end_time;
    private $query_process_time_milliseconds;
    private $query_mysql_result;

    // Persistent state variables
    private $active = true;
    public $query_history;
    public $query_total_process_time_milliseconds = 0;

    // Constructor
    function __construct($class = NULL)
    {
        // Load dependencies
        if(!$this->CI) $this->CI =& get_instance();

        // If query logging is off
        if(!$this->CI->config->item('log_queries'))
        {
            $this->deactivate();
            return;
        }

        // Initialize firephp
        if(!isset($this->CI->firephp) && $this->CI->config->item('log_to_firephp'))
        {
            $this->CI->load->library('firephp');
            $this->CI->firephp->log('Dev Log FirePHP integration initialized...');
        }

        // Initialize system logging
        if($this->CI->config->item('log_to_system'))
        {
            $this->CI->log->write_log('debug', "Dev Log system logging initialized...");
        }

        $this->initialize();
    }

    // Reset temporary state variables
    private function initialize()
    {
        $this->query_sql = '';
        $this->query_start_time = 0;
        $this->query_end_time = 0;
        $this->query_process_time_milliseconds = 0;
        $this->query_mysql_result = false;
    }

    // Public active state getter
    public function is_active()
    {
        return $this->active;
    }

    // Activates the enhanced dev log
    public function activate()
    {
        $this->active = true;
    }

    // Deactivates the enhanced dev log
    public function deactivate()
    {
        $this->active = false;
        $this->initialize();
    }

    // This method is called immediately before a query is executed using the CI DB class
    public function pre_query_hook($sql = false)
    {
        $this->initialize();
        $this->query_sql = $sql;
        $this->query_start_time = microtime(true);
    }

    // This method is called immediately after a query has been executed using the CI DB class
    public function post_query_hook($result = false)
    {
        $this->query_end_time = microtime(true);

        $this->query_process_time_milliseconds = ($this->query_end_time - $this->query_start_time) * 1000;
        $this->query_total_process_time_milliseconds += $this->query_process_time_milliseconds;

        $this->query_mysql_result = $result;

        $this->query_history[] = array(
            'sql' => $this->query_sql,
            'query_process_time_milliseconds' => $this->query_process_time_milliseconds
        );

        if($this->CI->config->item('log_to_firephp')) $this->render_to_firephp();
        if($this->CI->config->item('log_to_system')) $this->render_to_system();
    }

    public function summary_hook()
    {
        if($this->CI->config->item('log_to_firephp')) $this->CI->firephp->log('Total query time: ' . number_format($this->query_total_process_time_milliseconds, 2) . 'ms');
        if($this->CI->config->item('log_to_system')) $this->CI->log->write_log('debug', 'Total query time: ' . number_format($this->query_total_process_time_milliseconds, 2) . 'ms');

        return;
    }

    // This method renders the results to FirePHP
    public function render_to_firephp()
    {
        // Generate collapsable group
        $query_details = "SQL " . number_format($this->query_process_time_milliseconds, 2) . " ms:\n" . $this->query_sql;

        // If there is no result data set then we simply want to display the query details as a regular log item
        if(!$this->query_mysql_result || is_numeric($this->query_mysql_result) || !$this->CI->config->item('log_results') || mysql_num_rows($this->query_mysql_result) == 0)
        {
            $this->CI->firephp->log($query_details);
            return;
        }

        /** There appears to be a valid result set and we have received approval for logging results **/

        // Generate a FirePHP group header
        $this->CI->firephp->group($query_details, array('Collapsed' => true));

        // This is just lazy
        $query_results_num_rows = mysql_num_rows($this->query_mysql_result);

        $data_table = array();

        for($i=0; $i < $query_results_num_rows; $i++)
        {
            if($this->CI->config->item('log_maximum_result_count') && $i > $this->CI->config->item('log_maximum_result_count')) break;

            $row = mysql_fetch_assoc($this->query_mysql_result);

            if(empty($data_table))
            {
                $data_table[] = array_keys($row);
            }

            $data_table[] = array_values($row);
        }

        $this->CI->firephp->table($query_results_num_rows . ' results. ' . (count($data_table)-1) . ' shown.', $data_table);

        $this->CI->firephp->groupEnd();

        // Make sure that we reset the pointer of the mysql_result
        mysql_data_seek($this->query_mysql_result, 0);
    }

    // This method renders the results to the CodeIgniter system log
    public function render_to_system()
    {
        // Generate collapsable group
        $query_details = "\n------------------------------\n" . $this->query_sql . "\nTime: " . number_format($this->query_process_time_milliseconds, 2) . " ms\n------------------------------";

        $this->CI->log->write_log('debug', $query_details);

        // If there is no result data set then we simply want to display the query details as a regular log item
        if(!$this->query_mysql_result || is_numeric($this->query_mysql_result) || !$this->CI->config->item('log_results') || mysql_num_rows($this->query_mysql_result) == 0)
        {
            return;
        }

        /** There appears to be a valid result set and we have received approval for logging results **/

        $query_results_num_rows = mysql_num_rows($this->query_mysql_result);

        $data_table = '';

        for($i=0; $i < $query_results_num_rows; $i++)
        {
            if($this->CI->config->item('log_maximum_result_count') && $i > $this->CI->config->item('log_maximum_result_count')) break;

            $row = mysql_fetch_assoc($this->query_mysql_result);

            if(empty($data_table))
            {
                $data_table .= implode(',', array_keys($row)) . "\n";
            }

            $data_table .= implode(',', array_values($row)) . "\n";
        }

        $this->CI->log->write_log('debug', "\n" .
                                            "------------------------------\n" .
                                            $data_table .
                                            "------------------------------\n" .
                                            "\n" . $query_results_num_rows . ' results. ' . $i . " shown.\n");

        mysql_data_seek($this->query_mysql_result, 0);
    }
}
