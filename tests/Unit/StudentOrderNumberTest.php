<?php

namespace Tests\Unit;
use Tests\TestCase;

class StudentOrderNumberTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_console_command()
    {
        $this->artisan('student:order')
            ->expectsOutput('send successfully')
            ->assertExitCode(0);

        $this->artisan('student:order')->assertSuccessful();
    }
}
