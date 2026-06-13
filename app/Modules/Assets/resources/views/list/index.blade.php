@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('assets.list.create') }}" class="btn btn-primary pull-right">Add New Asset</a>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-default pull-right" style="margin-right: 10px;">Back to Dashboard</a>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Assets Inventory</div>
            <table class="table table-bordered table-hover" id="assetsTable">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Condition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" class="form-control input-sm" placeholder="Code"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Name"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Category"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Brand"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Model"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Serial"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Status"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Condition"/></th>
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
        var table = $('#assetsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("assets.datatable") }}',
            columns: [
                {data: 'asset_code', name: 'asset_code'},
                {data: 'asset_name', name: 'asset_name'},
                {data: 'category_id', name: 'category_id'},
                {data: 'brand', name: 'brand'},
                {data: 'model', name: 'model'},
                {data: 'serial_number', name: 'serial_number'},
                {data: 'current_status', name: 'current_status'},
                {data: 'condition', name: 'condition'},
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
