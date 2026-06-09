@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{route('recruitment.interviews.create')}}" class="btn btn-primary pull-right">Schedule New Interview</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Interviews</div>
            <table class="table table-bordered table-hover" id="interviewsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Candidate</th>
                        <th>Date & Time</th>
                        <th>Interviewer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="text" placeholder="ID" class="form-control input-sm"/></th>
                        <th><input type="text" placeholder="Candidate" class="form-control input-sm"/></th>
                        <th></th>
                        <th><input type="text" placeholder="Interviewer" class="form-control input-sm"/></th>
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
        var table = $('#interviewsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("recruitment.interviews.datatable")}}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'candidate', name: 'candidate_id'},
                {data: 'interview_date', name: 'interview_date'},
                {data: 'interviewer_name', name: 'interviewer_name'},
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
