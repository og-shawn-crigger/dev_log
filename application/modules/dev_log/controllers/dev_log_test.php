<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Dev log test controller
class dev_log_test extends CI_Controller
{
    public function index()
    {
        // Execute a query to verify that logs are being filled prcperly
        $this->db->get('dev_log_test_table');

        $this->load->view('dev_log_test/index');
    }
}