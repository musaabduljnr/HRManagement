<div class="form-group">
    {!! Form::label('title', 'Policy Title:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'e.g. Remote Work Policy']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('category', 'Category:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::select('category', $categories, null, ['class' => 'form-control', 'placeholder' => '-- Select Category --']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('content', 'Policy Content:', ['class' => 'col-sm-3']) !!}
    <div class="col-sm-6">
        {!! Form::textarea('content', null, ['class' => 'form-control', 'rows' => 12, 'placeholder' => 'Enter the full policy text here...']) !!}
    </div>
</div>
@include('errors._form-errors')
<hr>
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        <a href="{{ route('hr_policies.index') }}" class="btn btn-default">{{ trans('app.cancel') }}</a>
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
    </div>
</div>
