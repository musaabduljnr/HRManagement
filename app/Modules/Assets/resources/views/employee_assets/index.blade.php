@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('pim.employees.assets.create', $employee->id) }}" class="btn btn-primary pull-right">Assign / Checkout Asset</a>
        <a href="{{ route('pim.employees.edit', $employee->id) }}" class="btn btn-default pull-right" style="margin-right: 10px;">Back to Details</a>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Assigned Assets for: {{ $employee->first_name }} {{ $employee->last_name }}</div>
            <table class="table table-bordered table-hover" id="employeeAssetsTable">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Issue Date</th>
                        <th>Expected Return</th>
                        <th>Actual Return</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" class="form-control input-sm" placeholder="Asset"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Issue Date"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Expected Return"/></th>
                        <th><input type="text" class="form-control input-sm" placeholder="Actual Return"/></th>
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
        var table = $('#employeeAssetsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("pim.employees.assets.datatable", $employee->id) }}',
            columns: [
                {data: 'asset_id', name: 'asset_id'},
                {data: 'issue_date', name: 'issue_date'},
                {data: 'expected_return_date', name: 'expected_return_date'},
                {data: 'actual_return_date', name: 'actual_return_date'},
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
