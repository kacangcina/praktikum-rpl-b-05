<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserCapabilityTest extends TestCase
{
    public function test_regular_user_can_publish_recipes_but_cannot_upload_videos(): void
    {
        // Arrange
        $user = new User([
            'role' => 'user',
            'is_verified' => false,
        ]);

        // Act
        $canPublishRecipes = $user->canPublishRecipes();
        $canUploadVideos = $user->canUploadVideos();

        // Assert
        $this->assertTrue($canPublishRecipes);
        $this->assertFalse($canUploadVideos);
    }

    public function test_verified_creator_can_publish_recipes_and_upload_videos(): void
    {
        // Arrange
        $creator = new User([
            'role' => 'creator',
            'is_verified' => true,
        ]);

        // Act
        $canPublishRecipes = $creator->canPublishRecipes();
        $canUploadVideos = $creator->canUploadVideos();

        // Assert
        $this->assertTrue($canPublishRecipes);
        $this->assertTrue($canUploadVideos);
    }

    public function test_unverified_creator_is_treated_as_user_for_display_label(): void
    {
        // Arrange
        $creator = new User([
            'role' => 'creator',
            'is_verified' => false,
        ]);

        // Act
        $roleLabel = $creator->role_label;

        // Assert
        $this->assertSame('User', $roleLabel);
    }

    public function test_admin_role_is_detected_correctly(): void
    {
        // Arrange
        $admin = new User([
            'role' => 'admin',
        ]);

        // Act
        $isAdmin = $admin->isAdmin();
        $roleLabel = $admin->role_label;

        // Assert
        $this->assertTrue($isAdmin);
        $this->assertSame('Admin', $roleLabel);
    }

    public function test_user_initials_use_first_letters_from_name(): void
    {
        // Arrange
        $user = new User([
            'name' => 'Naufal Pratama',
            'username' => 'naufal',
        ]);

        // Act
        $initials = $user->initials;

        // Assert
        $this->assertSame('NP', $initials);
    }
}
