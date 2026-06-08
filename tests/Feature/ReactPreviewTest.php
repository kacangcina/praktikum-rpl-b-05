<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReactPreviewTest extends TestCase
{
    public function test_login_renders_the_react_shell(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('id="root"', false);
    }
}
