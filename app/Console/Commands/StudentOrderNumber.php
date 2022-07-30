<?php

namespace App\Console\Commands;
use App\Models\School;
use App\Models\Student;
use Illuminate\Console\Command;
use App\Mail\OrderNumber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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
        $orders = School::with('order')->get()->pluck('order.id','id')->toArray();

        Student::whereIn('id',$orders)->update(['order'=> 1]);

        $students = Student::with('school')
                    ->where('order','<>',1)
                    ->orderBy(School::select('id')->whereColumn('schools.id','students.school_id'))
                    ->orderBy('order')->get();

        $number = 1;
        foreach ($students as $student){
            $student->update(['order'=> $number++]);
        }

        $message = 'finished';
        Mail::to('no3man.mahmoud@gmail.com')->send(new OrderNumber($message));

        $this->info('send successfully');

    }
}
