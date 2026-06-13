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
    {!! Form::label('department_id', 'Department:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('department_id', $departments, null, ['class' => 'form-control', 'placeholder' => '-- Select Department --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('job_title_id', 'Job Title:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('job_title_id', $jobTitles, null, ['class' => 'form-control', 'placeholder' => '-- Select Job Title --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('employment_status', 'Employment Status:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('employment_status', $employmentStatuses, null, ['class' => 'form-control', 'placeholder' => '-- Select Status --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('password', 'Password:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => isset($employee) ? 'Leave blank to keep current' : 'Leave blank to auto-generate']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('bank_name', 'Bank Name:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('bank_name', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('account_number', 'Account Number:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('account_number', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('notes', trans('app.pim.employees.notes').':', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
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