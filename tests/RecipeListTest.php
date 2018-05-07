<?php
require_once 'BaseTestCase.php';

class RecipeListTest extends BaseTestCase
{
    protected function setUp()
    {
        $this->uri = 'http://web/recipes';
        $this->method = 'GET';
    }

    public function testSuccess()
    {
        [$header, $res] = $this->sendRequest();
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $res->status, 'Unexpected status code');
        $this->assertTrue(0 <= count($res->data), 'Unexpected respons data srtructure.');
    }
}
