@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('assets.categories.create') }}" class="btn btn-primary pull-right">Add New Category</a>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-default pull-right" style="margin-right: 10px;">Back to Dashboard</a>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Asset Categories</div>
            <table class="table table-bordered table-hover" id="categoriesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" class="form-control input-sm" placeholder="ID"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Name"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Description"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Status"/></th>
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
<style>
    tfoot input {
        width: 100%;
    }
</style>
@endsection

@section('additionalJS')
<script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function(){
        var table = $('#categoriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("assets.categories.datatable") }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
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
