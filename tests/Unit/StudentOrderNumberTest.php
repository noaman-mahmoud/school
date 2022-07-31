<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StudentOrderNumberTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_console_command()
    {
        $this->artisan('student:order')
            ->expectsOutput('send successfully');

        $this->artisan('student:order')->assertSuccessful();
    }
}
