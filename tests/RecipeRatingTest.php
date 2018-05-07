<?php
require_once 'BaseTestCase.php';

use App\Models\Recipe;
use App\Models\Rating;

class RecipeRatingTest extends BaseTestCase
{
    protected function setUp()
    {
        (new Recipe([
            'name' => 'RecipeRatingTest',
            'prep_time' => 15,
            'difficulty' => 2,
            'is_vegetarian' => 'f']))->save();
        $this->recipe = Recipe::all()[0];
        $this->uri = 'http://web/recipes/'.$this->recipe->id.'/rating';
        $this->method = 'POST';
    }

    protected function tearDown()
    {
        $recipes = Recipe::all();
        foreach ($recipes as $row) {
            $row->delete();
        }
        $ratings = Rating::all();
        foreach ($ratings as $row) {
            $row->delete();
        }
    }

    public function testSuccess()
    {
        $data = @json_encode(['score' => 3]);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json"]);
        $result = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $result->status, 'Unexpected status code');
        $this->assertEquals([], $result->data, 'Unexpected respons data srtructure.');
    }

    public function testMissigRequiredField()
    {
        // Misssing: score
        [$header, $res] = $this->sendRequest("{}", ["Content-type: application/json"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Missing Requied Field(s).", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testInvalidFormat()
    {
        // Invalid: score shoud 1~5.
        $data = @json_encode(['score' => 6]);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Invalid Format.", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testExtraField()
    {
        // Ignore extra field and proceed with rest of them.
        $data = @json_encode(['this_is_extra' => 1, 'score' => 3]);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(200, $res->status, 'Unexpected status code');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }

    public function testNotExist()
    {
        $this->recipe->delete();
        $data = @json_encode(['score' => 3]);
        [$header, $res] = $this->sendRequest($data, ["Content-type: application/json"]);
        $res = @json_decode($res);
        $this->assertEquals(200, $header['reponse_code'], 'Unexpected response code.');
        $this->assertEquals(400, $res->status, 'Unexpected status code');
        $this->assertEquals("Not Exists", $res->message, 'Unexpected respons message.');
        $this->assertEquals([], $res->data, 'Unexpected respons data srtructure.');
    }
}
