@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{route('recruitment.job-openings.create')}}" class="btn btn-primary pull-right">Add New Job Opening</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Job Openings</div>
            <table class="table table-bordered table-hover" id="jobOpeningsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" placeholder="ID" class="form-control input-sm"/></th>
                        <th><input type="text" placeholder="Title" class="form-control input-sm"/></th>
                        <th><input type="text" placeholder="Department" class="form-control input-sm"/></th>
                        <th><input type="text" placeholder="Status" class="form-control input-sm"/></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
@endsection

@section('additionalJS')
<script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        var table = $('#jobOpeningsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("recruitment.job_openings.datatable")}}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'department', name: 'department_id'},
                {data: 'status', name: 'status'},
                {data: 'actions', name: 'actions', sortable: false, searchable: false}
            ]
        });
        table.columns().every(function () {
            var that = this;
            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });
    });
</script>
@endsection
