<?php
require_once 'src/bootstrap.php';
class RoboFile extends \Robo\Tasks
{
    // use \Robo\Task\Development\loadTasks;
    use \Robo\Common\TaskIO;

    public function __construct()
    {
        $this->opencart_config = [
            'db_hostname' => 'localhost',
            'db_username' => 'root',
            'db_password' => 'root',
            'db_database' => 'oc_travis_test_db',
            'db_driver' => 'mysqli',
            'username' => 'admin',
            'password' => 'admin',
            'email' => 'travis@test.com',
            'http_server' => getenv('HTTP_SERVER')
        ];
    }

    public function opencartSetup()
    {
        $this->taskDeleteDir('www')->run();
        $this->taskFileSystemStack()
            ->mirror('vendor/opencart/opencart/upload', 'www')
            ->copy('src/upload/system/config/test-config.php', 'www/system/config/test-config.php')
            ->copy('src/upload/system/library/session/test.php', 'www/system/library/session/test.php')
            ->copy('src/upload/admin/controller/startup/test_startup.php','www/admin/controller/startup/test_startup.php')
            ->chmod('www', 0777, 0000, true)
            ->run();

        // Create new database, drop if exists already
        try {
            $conn = new PDO("mysql:host=" . $this->opencart_config['db_hostname'], $this->opencart_config['db_username'], $this->opencart_config['db_password']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("DROP DATABASE IF EXISTS `" . $this->opencart_config['db_database'] . "`");
            $conn->exec("CREATE DATABASE `" . $this->opencart_config['db_database'] . "`");
        } catch (PDOException $e) {
            $this->say("<error> Database error: " . $e->getMessage());
        }
        $conn = null;

        $install = $this->taskExec('php')->arg('www/install/cli_install.php')->arg('install');
        foreach ($this->opencart_config as $option => $value) {
            $install->option($option, $value);
        }

        ob_start();
        $install->run();
        ob_end_clean();

        $this->taskDeleteDir('www/install')->run();
    }

}