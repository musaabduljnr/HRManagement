@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #e1e1e1; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 30px;">
            <div class="panel-heading" style="background: #ffffff; color: #333; padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <h4 style="margin: 0; font-weight: 700; color: #1e3c72; font-size: 20px;">
                    <i class="glyphicon glyphicon-list-alt" style="margin-right: 10px;"></i> Attendance Records
                </h4>
                <div style="margin-top: 10px;">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addModal" style="border-radius: 8px; font-weight: 600; padding: 6px 15px;">
                        <i class="glyphicon glyphicon-plus"></i> Add Manual Record
                    </button>
                    <a href="{{ route('admin.attendance.scanner') }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 6px 15px; margin-left: 5px;">
                        <i class="glyphicon glyphicon-camera"></i> Scanner Terminal
                    </a>
                </div>
            </div>
            <div class="panel-body" style="padding: 20px;">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.attendance.index') }}" class="well form-inline" style="background-color: #fafbfc; border: 1px solid #eef0f2; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-right: 15px;">
                        <label for="date" style="margin-right: 8px; font-weight: 600; color: #555;">Date:</label>
                        <input type="date" id="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" style="border-radius: 6px; height: 36px;">
                    </div>
                    <div class="form-group" style="margin-right: 15px;">
                        <label for="employee" style="margin-right: 8px; font-weight: 600; color: #555;">Employee ID:</label>
                        <select name="user_id" id="employee" class="form-control" style="border-radius: 6px; height: 36px; min-width: 180px;">
                            <option value="">All Employees</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->first_name }} {{ $u->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="border-radius: 6px; height: 36px; padding: 0 20px; font-weight: 600;">
                        <i class="glyphicon glyphicon-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-default" style="border-radius: 6px; height: 36px; line-height: 34px; padding: 0 15px; margin-left: 5px;">
                        Clear
                    </a>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped" style="vertical-align: middle;">
                        <thead>
                            <tr style="color: #555; background-color: #f6f8fa;">
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                                <th>IP Address</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $record)
                                <tr>
                                    <td style="font-weight: 600; color: #333;">{{ $record->user->first_name }} {{ $record->user->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</td>
                                    <td style="color: #27ae60; font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                    </td>
                                    <td style="color: #c0392b; font-weight: 500;">
                                        {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : 'Active' }}
                                    </td>
                                    <td style="font-weight: 600;">
                                        @if($record->check_out)
                                            <?php
                                                $in = \Carbon\Carbon::parse($record->check_in);
                                                $out = \Carbon\Carbon::parse($record->check_out);
                                                $diff = $in->diffInMinutes($out);
                                                $hours = floor($diff / 60);
                                                $mins = $diff % 60;
                                                echo sprintf('%dh %02dm', $hours, $mins);
                                            ?>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><code style="font-size: 11px;">{{ $record->ip_address ?: '-' }}</code></td>
                                    <td class="text-right">
                                        <button class="btn btn-xs btn-primary edit-btn" 
                                                data-id="{{ $record->id }}" 
                                                data-name="{{ $record->user->first_name }} {{ $record->user->last_name }}"
                                                data-in="{{ \Carbon\Carbon::parse($record->check_in)->format('Y-m-dT\H:i') }}"
                                                data-out="{{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('Y-m-dT\H:i') : '' }}"
                                                style="border-radius: 4px; padding: 2px 8px;">
                                            <i class="glyphicon glyphicon-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.attendance.destroy', $record->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this attendance log?');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-xs btn-danger" style="border-radius: 4px; padding: 2px 8px; margin-left: 2px;">
                                                <i class="glyphicon glyphicon-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted" style="padding: 40px 0;">
                                        No attendance logs found for this date.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.attendance.store') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background-color: #27ae60; color: white; border: none; padding: 15px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addModalLabel" style="font-weight: 700;">Add Manual Attendance Record</h4>
                </div>
                <div class="modal-body" style="padding: 20px 25px;">
                    <div class="form-group">
                        <label for="add-user" style="font-weight: 600; color: #555;">Select Employee</label>
                        <select name="user_id" id="add-user" class="form-control" required style="border-radius: 6px;">
                            <option value="">Choose...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add-date" style="font-weight: 600; color: #555;">Date</label>
                        <input type="date" id="add-date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 6px;">
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="add-in" style="font-weight: 600; color: #555;">Check In Time</label>
                                <input type="datetime-local" id="add-in" name="check_in" class="form-control" required style="border-radius: 6px;">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="add-out" style="font-weight: 600; color: #555;">Check Out Time (Optional)</label>
                                <input type="datetime-local" id="add-out" name="check_out" class="form-control" style="border-radius: 6px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 15px 25px; background-color: #fafbfc;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 6px; font-weight: 600; padding: 6px 20px;">Save Record</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <form id="edit-form" method="POST">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background-color: #2980b9; color: white; border: none; padding: 15px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel" style="font-weight: 700;">Edit Attendance Record</h4>
                </div>
                <div class="modal-body" style="padding: 20px 25px;">
                    <div class="form-group">
                        <label style="font-weight: 600; color: #555;">Employee</label>
                        <input type="text" id="edit-name" class="form-control" readonly style="border-radius: 6px; background-color: #eee;">
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="edit-in" style="font-weight: 600; color: #555;">Check In Time</label>
                                <input type="datetime-local" id="edit-in" name="check_in" class="form-control" required style="border-radius: 6px;">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="edit-out" style="font-weight: 600; color: #555;">Check Out Time (Optional)</label>
                                <input type="datetime-local" id="edit-out" name="check_out" class="form-control" style="border-radius: 6px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 15px 25px; background-color: #fafbfc;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 6px; font-weight: 600; padding: 6px 20px;">Update Record</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('additionalJS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const checkIn = $(this).data('in');
        const checkOut = $(this).data('out');

        $('#edit-name').val(name);
        $('#edit-in').val(checkIn);
        $('#edit-out').val(checkOut);
        
        // Dynamic form action
        let action = "{{ route('admin.attendance.update', ':id') }}";
        action = action.replace(':id', id);
        $('#edit-form').attr('action', action);

        $('#editModal').modal('show');
    });
});
</script>
@endsection
