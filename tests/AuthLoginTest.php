<?php
require_once 'BaseTestCase.php';

class AuthLoginTest extends BaseTestCase
{
    protected function setUp()
    {
        $this->uriWithValidData = 'http://recipe:recipe@web/login';
        $this->uriWithInvalidData = 'http://dummy:data@web/login';
        $this->method = 'POST';
    }

    public function testSuccess()
    {
        $this->uri = $this->uriWithValidData;
        [$header, $res] = $this->sendRequest([]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $result->status, 'Unexpected status code');
        $this->assertTrue(isset($result->data->token), 'Unexpected respons data srtructure.');
        $this->assertTrue(isset($result->data->expire), 'Unexpected respons data srtructure.');
    }

    public function testWrongUsernamePassword()
    {
        $this->uri = $this->uriWithInvalidData;
        [$header, $res] = $this->sendRequest([]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Authentication Error.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
