<?php

namespace Tests\Feature\cinemas;

use Tests\TestCase;

class cinemaDeleteTest extends TestCase
{
    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testDeleteCinema()
    {
        $cinema = [
            'name_cinema' => 'CGV',
            'address' => 'hai chau',
            'amount_rooms' => '5',
        ];
        $newCinema = \App\Models\Cinema::create($cinema);
        $response = $this->deleteJson("/api/v1/cinemas/$newCinema->id", []);
        $response->assertStatus(204);
    }

    public function testCreateCinemaSuccess()
    {
        $newData = [
            'name_cinema' => 'CGV',
            'address' => 'hai chau',
            'amount_rooms' => '5',
        ];
        $response = $this->postJson('/api/v1/cinemas', $newData);
        $response->assertStatus(201);
        $response->assertJson([
            'title' => trans('messages.cinemas.storeSuccess'),
            'data' => [
                'type' => 'Cinema',
            ],
        ]);
        $response->assertJsonStructure([
            'status',
            'title',
            'data' => [
                'type',
                'id',
                'attributes',
            ],
        ]);

    }
}
