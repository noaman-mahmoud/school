<?php

namespace Tests\Unit;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StudentApiTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic unit test create.
     *
     * @return void
     */
    public function test_create_student()
    {
        $this->actingAs(User::factory()->create());

        $data = [
            'name' => 'test name',
            'school_id' => 1,
        ];

        $this->post(url('api/store-student'),$data)->assertStatus(200);
    }

    /**
     * A basic unit test show.
     *
     * @return void
     */
    public function test_show_student()
    {
        $this->actingAs(User::factory()->create());

        $student = Student::factory()->create();

        $this->get(url('api/show-student/'.$student->id))->assertStatus(200);
    }

    /**
     * A basic unit test update.
     *
     * @return void
     */
    public function test_update_task()
    {
        $this->actingAs(User::factory()->create());

        $student = Student::factory()->create();

        $student->name = "Updated name";
        $student->school_id = 1;
        $data = [
            'name' => "Updated name",
            'school_id' => 1,
        ];

        $response = $this->post(url('api/update-student'),$data);

        $response->assertStatus(200);
    }

    /**
     * A basic unit test delete.
     *
     * @return void
     */
    public function test_delete_student()
    {
        $this->actingAs(User::factory()->create());

        $student = Student::factory()->create();
        $data = [
            'student_id' => $student->id,
        ];

        $response = $this->post(url('api/delete-student'),$data);

        $response->assertStatus(200);

    }
}
