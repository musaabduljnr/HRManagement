<div class="form-group{{ $errors->has('asset_name') ? ' has-error' : '' }}">
    {!! Form::label('asset_name', 'Asset Name *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('asset_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('asset_name'))
            <span class="help-block">{{ $errors->first('asset_name') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
    {!! Form::label('category_id', 'Category *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('category_id', $categories, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Category --']) !!}
        @if ($errors->has('category_id'))
            <span class="help-block">{{ $errors->first('category_id') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('brand') ? ' has-error' : '' }}">
    {!! Form::label('brand', 'Brand', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('brand', null, ['class' => 'form-control']) !!}
        @if ($errors->has('brand'))
            <span class="help-block">{{ $errors->first('brand') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('model') ? ' has-error' : '' }}">
    {!! Form::label('model', 'Model', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('model', null, ['class' => 'form-control']) !!}
        @if ($errors->has('model'))
            <span class="help-block">{{ $errors->first('model') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('serial_number') ? ' has-error' : '' }}">
    {!! Form::label('serial_number', 'Serial Number', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('serial_number', null, ['class' => 'form-control']) !!}
        @if ($errors->has('serial_number'))
            <span class="help-block">{{ $errors->first('serial_number') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('purchase_date') ? ' has-error' : '' }}">
    {!! Form::label('purchase_date', 'Purchase Date', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('purchase_date', isset($asset) && $asset->purchase_date ? (is_string($asset->purchase_date) ? $asset->purchase_date : $asset->purchase_date->format('Y-m-d')) : null, ['class' => 'form-control']) !!}
        @if ($errors->has('purchase_date'))
            <span class="help-block">{{ $errors->first('purchase_date') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('purchase_cost') ? ' has-error' : '' }}">
    {!! Form::label('purchase_cost', 'Purchase Cost (₦)', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::number('purchase_cost', null, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) !!}
        @if ($errors->has('purchase_cost'))
            <span class="help-block">{{ $errors->first('purchase_cost') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('supplier') ? ' has-error' : '' }}">
    {!! Form::label('supplier', 'Supplier', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('supplier', null, ['class' => 'form-control']) !!}
        @if ($errors->has('supplier'))
            <span class="help-block">{{ $errors->first('supplier') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('warranty_expiry') ? ' has-error' : '' }}">
    {!! Form::label('warranty_expiry', 'Warranty Expiry', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('warranty_expiry', isset($asset) && $asset->warranty_expiry ? (is_string($asset->warranty_expiry) ? $asset->warranty_expiry : $asset->warranty_expiry->format('Y-m-d')) : null, ['class' => 'form-control']) !!}
        @if ($errors->has('warranty_expiry'))
            <span class="help-block">{{ $errors->first('warranty_expiry') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('current_status') ? ' has-error' : '' }}">
    {!! Form::label('current_status', 'Status *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('current_status', [
            'Available' => 'Available',
            'Assigned' => 'Assigned',
            'Under Maintenance' => 'Under Maintenance',
            'Damaged' => 'Damaged',
            'Lost' => 'Lost',
            'Retired' => 'Retired'
        ], null, ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('current_status'))
            <span class="help-block">{{ $errors->first('current_status') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('condition') ? ' has-error' : '' }}">
    {!! Form::label('condition', 'Condition *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('condition', [
            'Excellent' => 'Excellent',
            'Good' => 'Good',
            'Fair' => 'Fair',
            'Poor' => 'Poor',
            'Damaged' => 'Damaged'
        ], null, ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('condition'))
            <span class="help-block">{{ $errors->first('condition') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
    {!! Form::label('notes', 'Notes', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
        @if ($errors->has('notes'))
            <span class="help-block">{{ $errors->first('notes') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('image_file') ? ' has-error' : '' }}">
    {!! Form::label('image_file', 'Asset Photograph', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::file('image_file', ['class' => 'form-control']) !!}
        @if ($errors->has('image_file'))
            <span class="help-block">{{ $errors->first('image_file') }}</span>
        @endif
        @if(isset($asset) && $asset->image)
            <div style="margin-top: 10px;">
                <img src="{{ route('storage', ['path' => $asset->image]) }}" alt="Asset Photo" style="max-height: 100px; border-radius: 4px; border: 1px solid #ddd; padding: 4px;">
            </div>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
        <a href="{{ route('assets.list.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
