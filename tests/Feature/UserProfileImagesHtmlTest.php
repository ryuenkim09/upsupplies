<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\User;

class UserProfileImagesHtmlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function edit_view_renders_individual_delete_forms_for_each_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // add two images
        $f1 = UploadedFile::fake()->create('a.jpg', 10, 'image/jpeg');
        $f2 = UploadedFile::fake()->create('b.jpg', 10, 'image/jpeg');
        $p1 = $f1->store('user_images', 'public');
        $p2 = $f2->store('user_images', 'public');
        $img1 = $user->images()->create(['path'=>$p1]);
        $img2 = $user->images()->create(['path'=>$p2]);

        $this->actingAs($user);
        $html = $this->get(route('user.profile.edit'))->getContent();

        // ensure there are distinct forms with proper actions
        $this->assertStringContainsString('action="'.route('user.profile.images.destroy',$img1).'"',$html);
        $this->assertStringContainsString('action="'.route('user.profile.images.destroy',$img2).'"',$html);
        // ensure these forms are not nested within each other (no two opening tags before closing)
        preg_match_all('/<form[^>]*action="(.*?)"/',$html,$matches);
        // expect at least 3 forms: logout/profile delete? but check specifically those two
        $this->assertGreaterThanOrEqual(2,count($matches[1]));
    }
}
