<div class="form-group">
    {!! Form::label('candidate_id', 'Candidate:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('candidate_id', $candidates, null, ['class' => 'form-control', 'placeholder' => '-- Select Candidate --', 'required' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('interview_date', 'Interview Date:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::input('datetime-local', 'interview_date', null, ['class' => 'form-control', 'required' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('interviewer_name', 'Interviewer Name:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('interviewer_name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('status', 'Status:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('status', ['Scheduled' => 'Scheduled', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled'], null, ['class' => 'form-control', 'required' => true]) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('notes', 'Interview Notes:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 4]) !!}
    </div>
</div>
@include('errors._form-errors')
<hr>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        <a href="{{route('recruitment.interviews.index')}}" class="btn btn-default">Cancel</a>
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
    </div>
</div>
