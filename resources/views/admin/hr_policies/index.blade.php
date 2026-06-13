@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('hr_policies.create') }}" class="btn btn-primary pull-right" style="margin-bottom: 15px;">
            <i class="fa fa-plus"></i> Add New Policy
        </a>
    </div>
</div>

@if($policies->isEmpty())
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">HR Policies</div>
            <div style="padding: 40px; text-align: center; color: #888;">
                <i class="fa fa-file-text-o" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                <p>No HR policies have been created yet.</p>
                <a href="{{ route('hr_policies.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create First Policy
                </a>
            </div>
        </div>
    </div>
</div>
@else

{{-- Group policies by category --}}
@php $grouped = $policies->groupBy('category'); @endphp

@foreach($grouped as $category => $categoryPolicies)
<div class="row" style="margin-bottom: 10px;">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
                <span>
                    <i class="fa fa-folder-open-o" style="margin-right: 8px;"></i>
                    {{ $category }}
                    <span class="badge" style="margin-left: 6px;">{{ $categoryPolicies->count() }}</span>
                </span>
            </div>
            <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Policy Title</th>
                        <th style="width: 180px;">Last Updated</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryPolicies as $policy)
                    <tr>
                        <td>{{ $policy->id }}</td>
                        <td>
                            <strong>{{ $policy->title }}</strong>
                            <br>
                            <small class="text-muted" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; max-width: 600px;">
                                {{ str_limit(strip_tags($policy->content), 120) }}
                            </small>
                        </td>
                        <td>{{ $policy->updated_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('hr_policies.edit', $policy->id) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('hr_policies.destroy', $policy->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete policy \'{{ addslashes($policy->title) }}\'? This cannot be undone.');">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@endif

@endsection
