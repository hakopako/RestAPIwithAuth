<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;

class RecipeCreateTest extends BaseTestCase
{
    protected function setUp()
    {
        $this->token = $this->getToken();
        $this->uri = 'http://web/recipes';
        $this->method = 'POST';
    }

    protected function tearDown()
    {
        $recipes = Recipe::all();
        foreach ($recipes as $row) {
            $row->delete();
        }
    }

    protected function getToken(){
        $this->uri = 'http://recipe:recipe@web/login';
        $this->method = 'POST';
        [$header, $res] = $this->sendRequest();
        $result = @json_decode($res);
        return $result->data->token;
    }

    public function testSuccess()
    {
        $data = @json_encode(['name' => 'testSuccess', 'prep_time' => 15, 'difficulty' => 3, 'is_vegetarian' => 'f']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $result->status, 'Unexpected status code');
        $this->assertEquals([], $result->data, 'Unexpected respons data srtructure.');
    }

    public function testNoAuth()
    {
        $data = @json_encode(['name' => 'testSuccess', 'prep_time' => 15, 'difficulty' => 3, 'is_vegetarian' => 'f']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json"]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(403, $result->status, 'Unexpected status code');
        $this->assertEquals("Authentication Required", $result->message, 'Unexpected respons message.');
        $this->assertEquals([], $result->data, 'Unexpected respons data srtructure.');
    }

    public function testMissigRequiredField()
    {
        // Misssing: prep_time, difficulty, is_vegetarian
        $data = @json_encode(['name' => 'testMissigRequiredField']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Missing Requied Field(s).", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testInvalidFormat()
    {
        // Invalid: difficulty shoud 1~3.
        $data = @json_encode(['name' => 'testInvalidFormat', 'prep_time' => 15, 'difficulty' => 4, 'is_vegetarian' => 'f']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Invalid Format.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testExtraField()
    {
        // Ignore extra field and proceed with rest of them.
        $data = @json_encode(['this_is_extra' => 1, 'name' => 'testExtraField', 'prep_time' => 15, 'difficulty' => 3, 'is_vegetarian' => 'f']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $res->status, 'Unexpected status code');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
