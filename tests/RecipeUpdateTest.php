<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;

class RecipeUpdateTest extends BaseTestCase
{
    protected function setUp()
    {
        (new Recipe([
            'name' => 'RecipeUpdateTest',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        $this->recipe = Recipe::all()[0];
        $this->token = $this->getToken();
        $this->uri = 'http://web/recipes/'.$this->recipe->id;
        $this->method = 'PUT';
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
        $data = @json_encode(['name' => 'testSuccessUpdated']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $result->status, 'Unexpected status code');
        $this->assertEquals([], $result->data, 'Unexpected respons data srtructure.');
    }

    public function testNoAuth()
    {
        [$header, $res] = $this->sendRequest();
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(403, $res->status, 'Unexpected status code');
        $this->assertEquals("Authentication Required", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testInvalidFormat()
    {
        // Invalid: difficulty shoud 1~3.
        $data = @json_encode(['name' => 'testInvalidFormatUpdated', 'difficulty' => 4]);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Invalid Format.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testExtraField()
    {
        // return failure if any extra field is detected.
        $data = @json_encode(['this_is_extra' => 1, 'name' => 'testExtraFieldUpdated']);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json", "X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Invalid Format.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
