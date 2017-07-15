<?php

namespace Tests;

class SampleTest extends OpenCartTest
{
    public function testIsAdmin()
    {
        $this->assertFalse($this->isAdmin());
    }

    public function testDispatchingToExamplaryAction()
    {
        $response = $this->dispatchAction('account/login');
        $this->assertRegExp('/I am a returning customer/', $response->getOutput());
    }

    public function testDispatchingToAnotherExamplaryAction()
    {
        $response = $this->dispatchAction('checkout/cart/add', 'POST', ['product_id' => 28]);
        $output = json_decode($response->getOutput(), true);
        $this->assertTrue(isset($output['success']) && isset($output['total']));
        $this->assertRegExp('/HTC Touch HD/', $output['success']);
    }
}
