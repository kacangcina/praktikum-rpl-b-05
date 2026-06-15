<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cubu-blade-'.getmypid();
        File::ensureDirectoryExists($compiledPath);
        config(['view.compiled' => $compiledPath]);
    }
}
