<div class="form-group">
    {!! Form::label('first_name', trans('app.pim.employees.first_name').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('last_name', trans('app.pim.employees.last_name').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('email', trans('app.pim.employees.email').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::input('email', 'email', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('gender', trans('app.pim.employees.gender').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::label('male', trans('app.pim.employees.gender_male')) !!}
        {!! Form::radio('gender', 'm', @$employee->gender == 'm', ['id' => 'male']) !!}
        {!! Form::label('female', trans('app.pim.employees.gender_female')) !!}
        {!! Form::radio('gender', 'f', @$employee->gender == 'f', ['id' => 'female']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('birth_date', trans('app.pim.employees.birth_date').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::input('date', 'birth_date', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('notes', trans('app.pim.employees.notes').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('how_did_they_hear', trans('app.pim.employees.how_did_they_hear').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('how_did_they_hear', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('job_opening_id', 'Applying For:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('job_opening_id', $jobOpenings, @$application->job_opening_id, ['class' => 'form-control', 'placeholder' => '-- Select Job Opening --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('status', 'Application Status:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('status', [
            'Applied' => 'Applied',
            'Shortlisted' => 'Shortlisted',
            'Interviewing' => 'Interviewing',
            'Offered' => 'Offered',
            'Hired' => 'Hired',
            'Rejected' => 'Rejected'
        ], @$application->status, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('resume', 'Resume:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::file('resume', ['class' => 'form-control']) !!}
        @if(@$application->resume_path)
            <p class="help-block"><a href="{{ route('storage', str_replace('uploads/', '', $application->resume_path)) }}" target="_blank">View Uploaded Resume</a></p>
        @endif
    </div>
</div>
<div class="form-group">
    {!! Form::label('app_notes', 'Application Notes:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('app_notes', @$application->notes, ['class' => 'form-control', 'rows' => 3]) !!}
    </div>
</div>
@include('errors._form-errors')
<hr>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        <a href="{{route('pim.employees.index')}}" class="btn btn-default">{{trans('app.cancel')}}</a>
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
    </div>
</div>