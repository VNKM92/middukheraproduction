<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_package_via_patch_request()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $package = Package::create([
            'name' => 'Original Package Name',
            'slug' => 'original-package-name',
            'price_min' => 10000,
            'price_max' => 20000,
            'description' => 'Original description',
            'features' => ['Feature 1', 'Feature 2'],
            'image_path' => 'https://images.unsplash.com/photo-1',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.package.update', $package), [
            'name' => 'Updated Luxury Shoot',
            'price_min' => 25000,
            'price_max' => 45000,
            'description' => 'Updated luxury description with new scope.',
            'features' => '4K Ultra HD Video, Senior Director, 30 Plates',
            'image_url' => 'https://images.unsplash.com/photo-updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $package->refresh();
        $this->assertEquals('Updated Luxury Shoot', $package->name);
        $this->assertEquals(25000, $package->price_min);
        $this->assertEquals(45000, $package->price_max);
        $this->assertEquals('Updated luxury description with new scope.', $package->description);
        $this->assertEquals(['4K Ultra HD Video', 'Senior Director', '30 Plates'], $package->features);
        $this->assertEquals('https://images.unsplash.com/photo-updated', $package->image_path);
    }

    public function test_admin_can_update_package_with_image_file_upload()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'super_admin']);

        $package = Package::create([
            'name' => 'Upload Test Package',
            'slug' => 'upload-test-package',
            'price_min' => 15000,
            'price_max' => 30000,
            'description' => 'Upload test description',
            'features' => ['Perk 1'],
            'image_path' => 'https://images.unsplash.com/photo-default',
        ]);

        $fakeImage = UploadedFile::fake()->image('custom_shoot.jpg', 800, 600);

        $response = $this->actingAs($admin)->patch(route('admin.package.update', $package), [
            'name' => 'Upload Test Package',
            'price_min' => 15000,
            'price_max' => 30000,
            'description' => 'Upload test description with new image',
            'features' => 'Perk 1, Perk 2',
            'image' => $fakeImage,
        ]);

        $response->assertRedirect();
        $package->refresh();
        $this->assertStringContainsString('uploads/', $package->image_path);
    }
}
