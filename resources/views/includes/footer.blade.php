<div class="modal fade" tabindex="-1" role="dialog" id="confirm-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="{{trans('app.close')}}"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{{trans('app.confirm_action')}}</h4>
      </div>
      {{Form::open()}}
      <div class="modal-body">
        <p>{{trans('app.proceed_question')}}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('app.cancel')}}</button>
        {!! Form::submit(trans('app.confirm'), ['class' => 'btn btn-primary']) !!}
      </div>
      {!! Form::close() !!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script
  src="//code.jquery.com/jquery-3.1.1.min.js"
  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
  crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/main.js"></script>

<!-- ── Premium Error Handling UX & Required Fields Indicator JS ── -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Initialize Toast Container
    if (!document.getElementById('hrmToastContainer')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        container.id = 'hrmToastContainer';
        document.body.appendChild(container);
    }

    // 2. Toast Notification Helper
    window.showHrmToast = function(type, title, message) {
        const container = document.getElementById('hrmToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'hrm-toast toast-' + type;

        let iconHtml = '';
        if (type === 'success') iconHtml = '<i class="fa fa-check-circle hrm-toast-icon"></i>';
        else if (type === 'danger') iconHtml = '<i class="fa fa-exclamation-circle hrm-toast-icon"></i>';
        else if (type === 'warning') iconHtml = '<i class="fa fa-warning hrm-toast-icon"></i>';
        else iconHtml = '<i class="fa fa-info-circle hrm-toast-icon"></i>';

        toast.innerHTML = `
            ${iconHtml}
            <div class="hrm-toast-content">
                <h4 class="hrm-toast-title">${title}</h4>
                <p class="hrm-toast-message">${message}</p>
            </div>
            <button class="hrm-toast-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 50);

        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 5500);
    };

    // 3. Dynamic Required Field Asterisk Indicator
    document.querySelectorAll('form [required]').forEach(function(input) {
        let label = null;
        if (input.id) {
            label = document.querySelector('label[for="' + input.id + '"]');
        }
        if (!label) {
            let formGroup = input.closest('.form-group');
            if (formGroup) {
                label = formGroup.querySelector('label');
            }
        }
        if (label && !label.querySelector('.required-asterisk')) {
            let asterisk = document.createElement('span');
            asterisk.className = 'required-asterisk';
            asterisk.style.color = 'var(--danger)';
            asterisk.style.marginLeft = '4px';
            asterisk.style.fontWeight = 'bold';
            asterisk.textContent = '*';
            label.appendChild(asterisk);
        }
    });

    // 4. Trigger Laravel Validation Errors
    @if(isset($errors) && $errors->any())
        @foreach($errors->all() as $error)
            showHrmToast('danger', 'Validation Error', "{!! addslashes($error) !!}");
        @endforeach
    @endif

    // 5. Trigger Laravel Session Success/Error Toasts
    @if(session('success'))
        showHrmToast('success', 'Success', "{!! addslashes(session('success')) !!}");
    @endif
    @if(session('error'))
        showHrmToast('danger', 'Error', "{!! addslashes(session('error')) !!}");
    @endif
});
</script>