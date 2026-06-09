@extends('layouts.main_employee')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">My Payslips</div>
            <table class="table table-bordered table-hover" id="payslipsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Month</th>
                        <th>Base Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Bonuses</th>
                        <th>Net Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" placeholder="ID" class="form-control input-sm"/></th>
                        <th><input type="text" placeholder="Month" class="form-control input-sm"/></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
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
        var table = $('#payslipsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("employee.payroll.datatable")}}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'payroll_month', name: 'payroll_month'},
                {data: 'base_salary', name: 'base_salary'},
                {data: 'allowances', name: 'allowances'},
                {data: 'deductions', name: 'deductions'},
                {data: 'bonuses', name: 'bonuses'},
                {data: 'net_salary', name: 'net_salary'},
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
