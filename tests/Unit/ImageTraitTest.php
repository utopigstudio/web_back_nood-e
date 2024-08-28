<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ImageTrait;
use Intervention\Image\Laravel\Facades\Image;
use Mockery;

class ImageTraitTest extends TestCase
{
    use RefreshDatabase; 

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        Image::shouldReceive('make')->andReturnSelf();
        Image::shouldReceive('resize')->andReturnSelf();
        Image::shouldReceive('stream')->andReturn('image_stream');
    }

    public function test_uploads_image_to_local_storage()
    {
        Storage::fake('local');
        Mockery::mock(ImageTrait::class);

        $image = 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGNgYGBgAAAABQABpfZFQAAAAABJRU5ErkJggg==';

        $organization = Organization::create([
            'name' => 'Test organization',
            'description' => 'Test organization description',
            'image' => $image,
            'user_id' => 1
        ]);

        $uploadedImagePath =  'organization-' . $organization->image;

        Storage::disk('local')->exists($uploadedImagePath);

        $this->assertNotNull($organization->image);
        $this->assertStringStartsWith('organization-', $organization->image);
    }


    public function test_does_not_save_invalid_image()
    {
        Storage::fake('local');

        $this->expectException(\Exception::class);

        $invalidBase64Image = 'data:image/invalid;base64,' . base64_encode('invalid-data');

        Organization::create([
            'name' => 'Test Organization',
            'description' => 'Test Description',
            'image' => $invalidBase64Image,
            'user_id' => 1
        ]);
    }
}
