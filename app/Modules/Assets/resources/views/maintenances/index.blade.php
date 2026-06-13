@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('assets.maintenances.create') }}" class="btn btn-primary pull-right">Add Log Entry</a>
        <a href="{{ route('assets.dashboard') }}" class="btn btn-default pull-right" style="margin-right: 10px;">Back to Dashboard</a>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Asset Maintenance Logs</div>
            <table class="table table-bordered table-hover" id="maintenancesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Asset</th>
                        <th>Type</th>
                        <th>Cost</th>
                        <th>Service Provider</th>
                        <th>Maintenance Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" class="form-control input-sm" placeholder="ID"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Asset"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Type"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Cost"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Provider"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Date"/></th>
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
        var table = $('#maintenancesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("assets.maintenances.datatable") }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'asset_id', name: 'asset_id'},
                {data: 'maintenance_type', name: 'maintenance_type'},
                {data: 'cost', name: 'cost'},
                {data: 'service_provider', name: 'service_provider'},
                {data: 'maintenance_date', name: 'maintenance_date'},
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
