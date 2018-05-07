<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;

class RecipeSearchTest extends BaseTestCase
{
    protected function setUp()
    {
        (new Recipe([
            'name' => 'RecipeSearchTest_apple_pie',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        (new Recipe([
            'name' => 'RecipeSearchTest_curry',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        (new Recipe([
            'name' => 'RecipeSearchTest_apple_juice',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        $this->uri = 'http://web/recipes/search';
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
        $this->uri .= "?q=apple";
        [$header, $res] = $this->sendRequest([]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $result->status, 'Unexpected status code');
        $this->assertEquals(2, count($result->data), 'Unexpected respons data srtructure.');
    }

    public function testInvalidKeyword()
    {
        $this->uri .= "?q=\r\n--apple";
        [$header, $res] = $this->sendRequest([]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Invalid Keyword.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

}
