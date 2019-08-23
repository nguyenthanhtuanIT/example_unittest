<?php

namespace Tests\Feature\films;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class filmCreateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testCreateFilmSuccess()
    {
        $img = UploadedFile::fake()->create('text.img');
        $input = [
            'name_film' => 'lion king',
            'img' => $img,
            'projection_date' => '2019-08-12',
            'projection_time' => '120',
            'language' => 'english',
            'age_limit' => '>18',
            'detail' => 'phim bom tan',
            'trailer_url' => 'url:link',
            'price_film' => '10000',
            'curency' => 'd',
            'movies_type' => 'hanh dong',
        ];
        $resource = $this->postJson('/api/v1/films', $input);
        $resource->assertStatus(201);
    }

}
