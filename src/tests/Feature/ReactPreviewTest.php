<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReactPreviewTest extends TestCase
{
    public function test_application_renders_its_react_shell(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="root"', false);
    }

    public function test_react_shell_supports_client_side_routes(): void
    {
        $this->get('/recipes/1')
            ->assertOk()
            ->assertSee('id="root"', false);
    }
}
