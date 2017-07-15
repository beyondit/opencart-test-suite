<?php
namespace Tests;

class Sample2Test extends OpenCartTest
{

    public function testCreatingAndLoggingInACustomer()
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '1', store_id = '" . (int)$this->config->get('config_store_id') . "', firstname = 'Test', lastname = 'Customer', email = 'somebody@test.com', telephone = '123456789', fax = '123456789', custom_field = '', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1('password')))) . "', newsletter = '0', ip = '127.0.0.1', status = '1', date_added = NOW()");
        $customer_id = $this->db->getLastId();

        $this->assertTrue($this->login('somebody@test.com', 'password'));
        $this->assertTrue(!!$this->customer->isLogged());

        $response = $this->dispatchAction('account/edit');
        $this->assertRegExp('/Your Personal Details/', $response->getOutput());

        $this->logout();
        $this->assertFalse(!!$this->customer->isLogged());
    }

    public function testAnExamplaryModel()
    {
        $model = $this->loadModel("catalog/manufacturer");
        $manufacturer = $model->getManufacturer(5);
        $this->assertEquals('HTC', $manufacturer['name']);
    }
}
