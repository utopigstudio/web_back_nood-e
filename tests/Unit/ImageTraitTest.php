<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Intervention\Image\Laravel\Facades\Image;

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
        
        $tempImagePath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';
        $image = imagecreatetruecolor(100, 100); 
        $color = imagecolorallocate($image, 255, 0, 0); 
        imagefill($image, 0, 0, $color); 
        imagejpeg($image, $tempImagePath);

        $base64Image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($tempImagePath));

        $organization = Organization::create([
            'name' => 'Test Organization',
            'description' => 'Test Description',
            'image' => $base64Image,
            'user_id' => 1
        ]);

        $prefix = 'test-';
        $uploadedImagePath = $this->uploadImage($base64Image, $prefix);

        Storage::disk('local')->exists($uploadedImagePath);

        $this->assertNotNull($organization->image);
        $this->assertStringStartsWith('organization-', $organization->image);
        unlink($tempImagePath);
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
