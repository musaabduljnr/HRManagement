<div class="form-group{{ $errors->has('asset_id') ? ' has-error' : '' }}">
    {!! Form::label('asset_id', 'Asset *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        @if(isset($maintenance))
            {!! Form::select('asset_id', $assets, null, ['class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) !!}
            {!! Form::hidden('asset_id', $maintenance->asset_id) !!}
        @else
            {!! Form::select('asset_id', $assets, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Asset --']) !!}
        @endif
        @if ($errors->has('asset_id'))
            <span class="help-block">{{ $errors->first('asset_id') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('maintenance_type') ? ' has-error' : '' }}">
    {!! Form::label('maintenance_type', 'Maintenance Type *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('maintenance_type', [
            'Repairs' => 'Repairs',
            'Servicing' => 'Servicing',
            'Software upgrades' => 'Software upgrades',
            'Hardware replacement' => 'Hardware replacement'
        ], null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Maintenance Type --']) !!}
        @if ($errors->has('maintenance_type'))
            <span class="help-block">{{ $errors->first('maintenance_type') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
    {!! Form::label('description', 'Description *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required']) !!}
        @if ($errors->has('description'))
            <span class="help-block">{{ $errors->first('description') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('cost') ? ' has-error' : '' }}">
    {!! Form::label('cost', 'Cost (₦) *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::number('cost', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required' => 'required']) !!}
        @if ($errors->has('cost'))
            <span class="help-block">{{ $errors->first('cost') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('service_provider') ? ' has-error' : '' }}">
    {!! Form::label('service_provider', 'Service Provider', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('service_provider', null, ['class' => 'form-control']) !!}
        @if ($errors->has('service_provider'))
            <span class="help-block">{{ $errors->first('service_provider') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('maintenance_date') ? ' has-error' : '' }}">
    {!! Form::label('maintenance_date', 'Maintenance Date *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('maintenance_date', isset($maintenance) && $maintenance->maintenance_date ? (is_string($maintenance->maintenance_date) ? $maintenance->maintenance_date : $maintenance->maintenance_date->format('Y-m-d')) : \Carbon\Carbon::today()->format('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('maintenance_date'))
            <span class="help-block">{{ $errors->first('maintenance_date') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('next_maintenance_date') ? ' has-error' : '' }}">
    {!! Form::label('next_maintenance_date', 'Next Maintenance Date', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('next_maintenance_date', isset($maintenance) && $maintenance->next_maintenance_date ? (is_string($maintenance->next_maintenance_date) ? $maintenance->next_maintenance_date : $maintenance->next_maintenance_date->format('Y-m-d')) : null, ['class' => 'form-control']) !!}
        @if ($errors->has('next_maintenance_date'))
            <span class="help-block">{{ $errors->first('next_maintenance_date') }}</span>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
        <a href="{{ route('assets.maintenances.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
