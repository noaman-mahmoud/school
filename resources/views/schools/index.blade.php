@extends('layouts.master')

@section('content')
    <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">

            <div class="row g-3 mb-4 align-items-center justify-content-between">
                <div class="col-auto">
                    <h1 class="app-page-title mb-0">Schools</h1>
                </div>
                <div class="col-auto">
                    <div class="page-utilities">
                        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                            <div class="col-auto">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#storeModal">
                                    Create school
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
                                        <th class="cell">Edit</th>
                                        <th class="cell">Delete</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($schools as $school)
                                        <tr>
                                            <td class="cell">{{$loop->iteration}}</td>
                                            <td class="cell">
                                                <span class="truncate">{{$school->name}}</span>
                                            </td>
                                            <td class="cell">
                                                <button type="button" class="btn btn-primary" data-school="{{$school}}"
                                                        data-toggle="modal" data-target="#updateModal">
                                                    Edit
                                                </button>
                                            </td>

                                            <td class="cell">
                                                <form action="{{ route('schools.destroy',$school->id) }}" method="Post">
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
                        @if ($schools->hasPages())
                            <div class="pagination-wrapper">
                                {{ $schools->links() }}
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
                            <h5 class="modal-title" id="exampleModalLabel">Create new School</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form method="post" action="{{route('schools.store')}}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="email1">name</label>
                                    <input type="text" class="form-control" name="name" required id="name"
                                        value="{{old('name')}}" aria-describedby="emailHelp" placeholder="Enter name">
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
                            <h5 class="modal-title" id="exampleModalLabel">update School</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="" id="schoolUpdate">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="email1">name</label>
                                    <input type="hidden" name="id" id="school_id">
                                    <input type="text" class="form-control" name="name" required id="edit_name"
                                           aria-describedby="emailHelp" placeholder="Enter name">
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
            var button  = $(event.relatedTarget);
            var school  = button.data('school')
            var url     ='{{route("schools.update", ":id") }}';
                url     = url.replace(':id', school.id);

            $('#schoolUpdate').attr('action', url);
            $('#school_id').val(school.id);
            $('#edit_name').val(school.name);
        });
    });
</script>
@endsection
