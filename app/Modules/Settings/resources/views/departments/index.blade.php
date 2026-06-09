@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{route('settings.departments.create')}}" class="btn btn-primary pull-right">Add New Department</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Departments</div>
            <table class="table table-bordered table-hover" id="departmentsTable">
                <thead>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th></th>
                </thead>
                <tfoot>
                    <th><input type="text" placeholder="ID"/></th>
                    <th><input type="text" placeholder="Name"/></th>
                    <th><input type="text" placeholder="Description"/></th>
                    <th></th>
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
        var table = $('#departmentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("settings.departments.datatable")}}',
            columns: [
                {data: 0, name: 'id'},
                {data: 1, name: 'name'},
                {data: 2, name: 'description'},
                {data: 3, name: 'actions', sortable: false, searchable: false}
            ]
        });
        table.columns().every(function () {
            var that = this;
            $('input', this.footer()).on( 'keyup change', function () {
                if (that.search() !== this.value) {
                     that.search(this.value).draw();
                }
            });
        });
    });
</script>
@endsection
