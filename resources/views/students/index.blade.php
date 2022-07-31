@extends('layouts.master')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">

            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Students</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#storeModal">
                                    Create student
                                </button>
                            </div>
                        </div><!--//row-->
                    </div><!--//table-utilities-->
                </div><!--//col-auto-->
            </div><!--//row-->

            <div class="tab-content" id="orders-table-tab-content">
                <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                    <div class="app-card app-card-orders-table shadow-sm mb-5">
                        <div class="app-card-body">
                            <div class="table-responsive">
                                <table class="table app-table-hover mb-0 text-left">
                                    <thead>
                                    <tr>
                                        <th class="cell">#</th>
                                        <th class="cell">name</th>
                                        <th class="cell">school</th>
                                        <th class="cell">order</th>
                                        <th class="cell">Edit</th>
                                        <th class="cell">Delete</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($students as $student)
                                        <tr>
                                            <td class="cell">{{$loop->iteration}}</td>
                                            <td class="cell">
                                                <span class="truncate">{{$student->name}}</span>
                                            </td>
                                            <td class="cell">
                                                <span class="truncate">{{$student->school->name}}</span>
                                            </td>
                                            <td class="cell">
                                                <span class="truncate">{{$student->order}}</span>
                                            </td>
                                            <td class="cell">
                                                <button type="button" class="btn btn-primary" data-student="{{$student}}"
                                                        data-toggle="modal" data-target="#updateModal">
                                                    Edit
                                                </button>
                                            </td>

                                            <td class="cell">
                                                <form action="{{ route('students.destroy',$student->id) }}" method="Post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                       </tr>
                                      @endforeach
                                    </tbody>
                                </table>
                            </div><!--//table-responsive-->

                        </div><!--//app-card-body-->
                    </div><!--//app-card-->

                    <nav class="app-pagination">
                        @if ($students->hasPages())
                            <div class="pagination-wrapper">
                                {{ $students->links() }}
                            </div>
                        @endif
                    </nav><!--//app-pagination-->

                </div><!--//tab-pane-->
            </div><!--//tab-content-->


            <!-- Store Modal -->
            <div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0">
                            <h5 class="modal-title" id="exampleModalLabel">Create new Student</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form method="post" action="{{route('students.store')}}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">name</label>
                                    <input type="text" class="form-control" name="name" required id="name"
                                        value="{{old('name')}}" aria-describedby="emailHelp" placeholder="Enter name">
                                </div>
                                <div class="form-group">
                                    <label for="schools">schools</label>
                                    <select id="" name="school_id" class="form-control">
                                      @foreach($schools as $school)
                                        <option value="{{$school->id}}">{{$school->name}}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 d-flex justify-content-center">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- update Modal -->
            <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-0">
                            <h5 class="modal-title" id="exampleModalLabel">update Student</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="" id="studentUpdate">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="email1">name</label>
                                    <input type="hidden" name="id" id="student_id">
                                    <input type="text" class="form-control" name="name" required id="edit_name"
                                           aria-describedby="emailHelp" placeholder="Enter name">
                                </div>

                                <div class="form-group">
                                    <label for="schools">schools</label>
                                    <select id="schoolID" name="school_id" class="form-control schools">
                                        @foreach($schools as $school)
                                            <option value="{{$school->id}}">{{$school->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 d-flex justify-content-center">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div><!--//container-fluid-->
    </div><!--//app-content-->
@endsection
@section('script')
<script>
    $(document).ready(function () {
        $('#updateModal').on('show.bs.modal', function (event){
            var button   = $(event.relatedTarget);
            var student  = button.data('student')

            var url     ='{{route("students.update", ":id") }}';
                url     = url.replace(':id', student.id);

            $('#studentUpdate').attr('action', url);
            $('#student_id').val(student.id);
            $('#edit_name').val(student.name);
            $("#schoolID").val(student.school_id).change();
            // window.document.getElementById('select_element').selectedIndex = 4;
        });
    });
</script>
@endsection
