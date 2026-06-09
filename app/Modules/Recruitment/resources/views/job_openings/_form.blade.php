<div class="form-group">
    {!! Form::label('title', 'Job Title:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('title', null, ['class' => 'form-control', 'required' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('department_id', 'Department:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('department_id', $departments, null, ['class' => 'form-control', 'placeholder' => '-- Select Department --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('status', 'Status:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('status', ['Open' => 'Open', 'Closed' => 'Closed', 'Draft' => 'Draft'], null, ['class' => 'form-control', 'required' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('description', 'Job Description:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5, 'required' => true]) !!}
    </div>
</div>
@include('errors._form-errors')
<hr>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        <a href="{{route('recruitment.job-openings.index')}}" class="btn btn-default">Cancel</a>
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
    </div>
</div>
