@extends(Auth::user()->role == \App\User::USER_ROLE_EMPLOYEE ? 'layouts.main_employee' : 'layouts.main')

@section('content')
<div class="row">
    <!-- Policy List Column -->
    <div class="col-md-4">
        <div class="custom-panel">
            <div class="custom-panel-heading"><i class="fa fa-book"></i> Active HR Policies</div>
            <div class="panel-body" style="max-height: 450px; overflow-y: auto;">
                @forelse($policies as $policy)
                    <div style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <h4 style="margin-top: 0; color: #2e6da4;">{{ $policy->title }}</h4>
                        <span class="label label-info" style="margin-bottom: 5px; display:inline-block;">{{ $policy->category }}</span>
                        <p class="text-muted" style="font-size: 13px;">{{ str_limit($policy->content, 120) }}</p>
                    </div>
                @empty
                    <p class="text-center text-muted">No policy documents uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Chat Assistant Column -->
    <div class="col-md-8">
        <div class="custom-panel">
            <div class="custom-panel-heading"><i class="fa fa-android"></i> AI HR Assistant</div>
            <div class="panel-body">
                <!-- Suggestion Chips -->
                <div style="margin-bottom: 15px;">
                    <strong>Suggested Questions:</strong><br>
                    @foreach($suggestedQuestions as $q)
                        <button type="button" class="btn btn-default btn-xs suggested-btn" style="margin: 5px 5px 0 0; border-radius: 12px; padding: 4px 10px;" data-question="{{ $q }}">
                            {{ $q }}
                        </button>
                    @endforeach
                </div>
                
                <!-- Chat Message Window -->
                <div id="chat-window" style="height: 300px; border: 1px solid #ddd; border-radius: 4px; padding: 15px; overflow-y: scroll; background-color: #fafafa; margin-bottom: 15px;">
                    <div class="bot-msg" style="margin-bottom: 10px;">
                        <span style="background-color: #eee; padding: 8px 12px; border-radius: 4px; display: inline-block; max-width: 80%;">
                            Hello! I am your AI HR Assistant. Ask me anything about our company leaves, remote work policies, or code of conduct.
                        </span>
                    </div>
                </div>
                
                <!-- Chat Input Form -->
                <form id="chat-form">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" placeholder="Type your HR question here..." autocomplete="off" required>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit" id="send-btn">
                                <i class="fa fa-paper-plane"></i> Send
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additionalJS')
<script>
$(document).ready(function() {
    function scrollToBottom() {
        var chatWin = $('#chat-window');
        chatWin.scrollTop(chatWin[0].scrollHeight);
    }

    function appendMessage(text, sender) {
        var align = sender === 'user' ? 'text-right' : 'text-left';
        var bg = sender === 'user' ? '#d9edf7' : '#eee';
        var float = sender === 'user' ? 'pull-right' : 'pull-left';
        
        var msgHtml = '<div class="' + align + '" style="margin-bottom: 12px; overflow: hidden;">' +
            '<span class="' + float + '" style="background-color: ' + bg + '; padding: 8px 12px; border-radius: 4px; display: inline-block; max-width: 80%; text-align: left;">' +
            text +
            '</span>' +
            '</div>';
        
        $('#chat-window').append(msgHtml);
        scrollToBottom();
    }

    // Suggested questions click
    $('.suggested-btn').click(function() {
        var q = $(this).data('question');
        $('#chat-input').val(q);
        $('#chat-form').submit();
    });

    // Form submit
    $('#chat-form').submit(function(e) {
        e.preventDefault();
        var question = $('#chat-input').val().trim();
        if (!question) return;

        appendMessage(question, 'user');
        $('#chat-input').val('');
        
        // Show typing indicator
        var typingId = 'typing-' + Date.now();
        var typingHtml = '<div class="text-left" id="' + typingId + '" style="margin-bottom: 12px; overflow: hidden;">' +
            '<span class="pull-left" style="background-color: #eee; padding: 8px 12px; border-radius: 4px; display: inline-block; max-width: 80%; font-style: italic; color: #777;">' +
            'Typing...' +
            '</span>' +
            '</div>';
        $('#chat-window').append(typingHtml);
        scrollToBottom();

        $.ajax({
            url: "{{ route('assistant.ask') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                question: question
            },
            success: function(res) {
                $('#' + typingId).remove();
                appendMessage(res.answer, 'bot');
            },
            error: function() {
                $('#' + typingId).remove();
                appendMessage("Sorry, I encountered an error. Please try again.", 'bot');
            }
        });
    });
});
</script>
@endsection
