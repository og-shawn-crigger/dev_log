<pre>
dev_log output is: <?php echo $this->config->item('log_queries') ? 'enabled' : 'disabled'; ?>

dev_log system logging is: <?php echo $this->config->item('log_to_system') ? 'enabled' : 'disabled'; ?>

dev_log FirePHP logging is: <?php echo $this->config->item('log_to_firephp') ? 'enabled' : 'disabled'; ?>

dev_log result logging is: <?php echo $this->config->item('log_results') ? 'enabled' : 'disabled'; ?>

dev_log maximum result output count is: <?php echo $this->config->item('log_maximum_result_count'); ?>

<hr/>

Should logging be enabled and should you have received no errors you should see the test output in your system log and / or in Firebug.
</pre>