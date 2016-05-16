<?php

class RoboFile extends \Robo\Tasks
{
    use \Robo\Task\Development\loadTasks;
    use \Robo\Common\TaskIO;

    protected $config = [
        'db_hostname' => 'localhost',
        'db_username' => 'root',
        'db_password' => 'root',
        'db_database' => 'oc_travis_test_db',
        'db_driver' => 'mysqli',
        'username' => 'admin',
        'password' => 'admin',
        'email' => 'travis@test.com',
        'http_server' => 'http://localhost:8000/'
    ];

    public function travisOpencartSetup()
    {
        $this->taskDeleteDir('www')->run();
        $this->taskFileSystemStack()
            ->mirror('vendor/opencart/opencart/upload', 'www')
            ->copy('src/test-config.php', 'www/system/config/test-config.php')
            ->copy('src/test-catalog-startup.php','www/catalog/controller/startup/test_startup.php')
            ->copy('src/test-admin-startup.php','www/admin/controller/startup/test_startup.php')
            ->chmod('www', 0777, 0000, true)
            ->run();

        // Create new database, drop if exists already
        try {
            $conn = new PDO("mysql:host=" . $this->config['db_hostname'], $this->config['db_username'], $this->config['db_password']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("DROP DATABASE IF EXISTS `" . $this->config['db_database'] . "`");
            $conn->exec("CREATE DATABASE `" . $this->config['db_database'] . "`");
        } catch (PDOException $e) {
            $this->printTaskError("<error> Could not connect ot database...");
        }
        $conn = null;

        $install = $this->taskExec('php')->arg('www/install/cli_install.php')->arg('install');
        foreach ($this->config as $option => $value) {
            $install->option($option, $value);
        }
        $install->run();
        $this->taskDeleteDir('www/install')->run();
    }

}