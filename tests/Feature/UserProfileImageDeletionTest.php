<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserProfileImageDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_delete_a_profile_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        // create a fake file and associate it with the user
        $file = UploadedFile::fake()->create('pic.jpg', 50, 'image/jpeg');
        $path = $file->store('user_images', 'public');
        $img = $user->images()->create(['path' => $path]);

        // make sure it exists
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists($path);

        $response = $this->delete(route('user.profile.images.destroy', $img));
        $response->assertRedirect(route('user.profile.edit'));
        $response->assertSessionHas('status', 'Image removed.');

        $disk->assertMissing($path);
        $this->assertDatabaseMissing('user_images', ['id' => $img->id]);
    }
}
