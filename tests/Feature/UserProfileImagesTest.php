<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserProfileImagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_upload_multiple_images_to_profile()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        // prepare two fake files with the correct mime types.  We use
        // create() here instead of ->image() because the GD extension isn't
        // installed in the test environment and ->image() would throw.
        $file1 = UploadedFile::fake()->create('photo1.jpg', 100, 'image/jpeg');
        $file2 = UploadedFile::fake()->create('photo2.png', 150, 'image/png');

        $response = $this->put(route('user.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            // no password change
            'images' => [$file1, $file2],
        ]);

        $response->assertRedirect(route('user.profile.edit'));
        $response->assertSessionHas('status', 'Profile updated.');

        // ensure files were stored and DB records created
        // grab the fake disk so static analyzers know its type
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('user_images/'.$file1->hashName());
        $disk->assertExists('user_images/'.$file2->hashName());

        $this->assertCount(2, $user->fresh()->images);
    }
}
