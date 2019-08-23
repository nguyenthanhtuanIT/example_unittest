<?php

namespace Tests\Feature\cinemas;

use Tests\TestCase;

class cinemaListTest extends TestCase
{
    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetListCinemaSuccess()
    {
        $response = $this->getJson('/api/v1/cinemas');
        $response->assertStatus(200);
        $response->assertJson([
            'title' => trans('messages.cinemas.getListSuccess'),
            'data' => [[
                'type' => 'Cinema']],
        ]);
        $response->assertJsonStructure([
            'status',
            'title',
            'data' => [[
                'type',
                'id',
                'attributes',
            ]],
        ]);
    }
}
