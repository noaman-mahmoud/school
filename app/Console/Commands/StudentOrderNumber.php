<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\Models\School;
use App\Models\Student;
use App\Mail\OrderNumber;

class StudentOrderNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $students = Student::with('school')
                    ->orderBy(School::select('id')->whereColumn('schools.id','students.school_id'))
                    ->orderBy('order')->get();

        foreach ($students as $student){

            $count   = 1;
            $numbers = $student->where('school_id',$student->school_id)->get();

            foreach ($numbers as $number){
                $number->update(['order'=>$count]);
                $count ++;
            }
        }


        $message = 'finished';
        Mail::to('no3man.mahmoud@gmail.com')->send(new OrderNumber($message));

        $this->info('send successfully');

    }
}
