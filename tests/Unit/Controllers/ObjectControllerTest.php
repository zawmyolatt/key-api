<?php

namespace Tests\Controllers;

use App\Models\ObjectData;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetIndexSuccess()
    {
        ObjectData::factory()->count(5)->create();

        $response = $this->get('/api/object/get_all_records');
        
        $response->assertStatus(200);

        $responseData = $response->decodeResponseJson();

        $this->assertEquals(5, sizeof($responseData['data']));
        $firstData = $responseData['data'][0];
        $this->assertArrayHasKey('id', $firstData);
        $this->assertArrayHasKey('key', $firstData);
        $this->assertArrayHasKey('value', $firstData);
        $this->assertArrayHasKey('timestamp', $firstData);
        $this->assertArrayHasKey('created_at', $firstData);
    }

    public function testCreateValidationErrors()
    {
        $response = $this->post('/api/object/',[]);
        $response->assertStatus(400)->assertJson([
            "http_code" => 400,
            "message" => "Invalid Json Body Content in Request"
        ]);

        $response = $this->post('/api/object/',['mykey' => '']);
        $response->assertStatus(400)->assertJson([
            'http_code' => 400,
            'message' => 'Bad Request',
            'errors' => [
                'mykey' => [ 'The mykey field is required.']
            ]
        ]);
    }

    public function testCreateSuccess()
    {
        $response = $this->post('/api/object/',[
            'mykey' => 'value1'
        ]);
        $response->assertStatus(201);
        $responseData = $response->decodeResponseJson();
        $objectResponseData = $responseData['data'];
        $this->assertEquals('mykey', $objectResponseData['key']);
        $this->assertEquals('value1', $objectResponseData['value']);
    }

    public function testGetShowSuccess()
    {
        $objectData = ObjectData::factory()->create();

        $response = $this->get('/api/object/'.$objectData->key);
        
        $response->assertStatus(200);

        $responseData = $response->decodeResponseJson();
        $objectResponseData = $responseData['data'];
        $this->assertEquals([
            'id' => $objectData->id,
            'key' => $objectData->key,
            'value' => $objectData->value,
            'timestamp' => $objectData->created_at->timestamp,
            'created_at' => (string) $objectData->created_at
        ], $objectResponseData);
    }

    public function testGetShowSuccessWithSameKey()
    {
        ObjectData::factory()->create([
            'key' => 'mykey',
            'value' => 'value1',
            'created_at' => Carbon::parse('2021-02-10 18:00:00'),
            'created_at' => Carbon::parse('2021-02-10 18:00:00')
        ]);

        $objectData2 = ObjectData::factory()->create([
            'key' => 'mykey',
            'value' => 'value2',
            'created_at' => Carbon::parse('2021-02-10 18:05:00'),
            'created_at' => Carbon::parse('2021-02-10 18:05:00')
        ]);

        $response = $this->get('/api/object/mykey');
        
        $response->assertStatus(200);

        $responseData = $response->decodeResponseJson();
        $objectResponseData = $responseData['data'];
        $this->assertEquals([
            'id' => $objectData2->id,
            'key' => 'mykey',
            'value' => 'value2',
            'created_at' => '2021-02-10 18:05:00',
            'timestamp' => 1612980300
        ], $objectResponseData);
    }

    public function testGetShowSuccessWithTimestampFilter()
    {
        $objectData1 = ObjectData::factory()->create([
            'key' => 'mykey',
            'value' => 'value1',
            'created_at' => Carbon::parse('2021-02-10 18:00:00'),
            'created_at' => Carbon::parse('2021-02-10 18:00:00')
        ]);

        ObjectData::factory()->create([
            'key' => 'mykey',
            'value' => 'value2',
            'created_at' => Carbon::parse('2021-02-10 18:05:00'),
            'created_at' => Carbon::parse('2021-02-10 18:05:00')
        ]);
        $requestTimestamp = Carbon::parse('2021-02-10 18:03:00')->timestamp;
        $response = $this->get('/api/object/mykey?timestamp='. $requestTimestamp);
        
        $response->assertStatus(200);

        $responseData = $response->decodeResponseJson();
        $objectResponseData = $responseData['data'];
        $this->assertEquals([
            'id' => $objectData1->id,
            'key' => 'mykey',
            'value' => 'value1',
            'created_at' => '2021-02-10 18:00:00',
            'timestamp' => 1612980000
        ], $objectResponseData);
    }
}
