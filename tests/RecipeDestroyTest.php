<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;

class RecipeDestroyTest extends BaseTestCase
{
    protected function setUp()
    {
        (new Recipe([
            'name' => 'RecipeDestroyTest',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        $this->recipe = Recipe::all()[0];
        $this->token = $this->getToken();
        $this->uri = 'http://web/recipes/'.$this->recipe->id;
        $this->method = 'DELETE';
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
        [$header, $res] = $this->sendRequest("", ["X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $res->status, 'Unexpected status code');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
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

    public function testNotExist()
    {
        $this->recipe->delete();
        [$header, $res] = $this->sendRequest("", ["X-RECIPE-TOKEN: $this->token"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Not Exists", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
