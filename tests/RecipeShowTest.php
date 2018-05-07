<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;

class RecipeShowTest extends BaseTestCase
{
    protected function setUp()
    {
        (new Recipe([
            'name' => 'RecipeShowTest',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        $this->recipe = Recipe::all()[0];
        $this->uri = 'http://web/recipes/'.$this->recipe->id;
        $this->method = 'GET';
    }

    protected function tearDown()
    {
        $recipes = Recipe::all();
        foreach ($recipes as $row) {
            $row->delete();
        }
    }

    public function testSuccess()
    {
        [$header, $res] = $this->sendRequest();
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $res->status, 'Unexpected status code');
        $this->assertEquals($this->recipe->toArray(), (array)$res->data, 'Unexpected respons.');
    }

    public function testNotExist()
    {
        $this->recipe->delete();
        [$header, $res] = $this->sendRequest();
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Not Exists", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
