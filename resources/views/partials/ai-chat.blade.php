<div class="aic" id="aic" hidden data-url="{{ route('ai.chat') }}" data-token="{{ csrf_token() }}" data-name="{{ $site['site_name'] ?? 'Reganto IT Solution' }}">
    <div class="aic-back" data-aic-close></div>
    <section class="aic-box" role="dialog" aria-modal="true" aria-label="Chat with Reganto AI">
        <header class="aic-head">
            <span class="aic-avatar"><x-icon name="bot" size="22" /><i></i></span>
            <div class="aic-who"><b>Reganto AI</b><small>Online · ask me anything about our work</small></div>
            <button type="button" class="aic-reset" data-aic-reset title="New chat" aria-label="New chat">↺</button>
            <button type="button" class="aic-x" data-aic-close aria-label="Close chat">×</button>
        </header>
        <div class="aic-log" id="aicLog" aria-live="polite"></div>
        <div class="aic-chips" id="aicChips">
            <button type="button">What services do you offer?</button>
            <button type="button">Show your recent projects</button>
            <button type="button">How can I book a service?</button>
            <button type="button">Where is your office?</button>
        </div>
        <form class="aic-form" id="aicForm" autocomplete="off">
            <input type="text" id="aicInput" maxlength="500" placeholder="Type a message…" aria-label="Message">
            <button type="submit" aria-label="Send"><x-icon name="send" size="18" /></button>
        </form>
    </section>
</div>
<script src="{{ asset('assets/js/ai-chat.js') }}?v={{ filemtime(public_path('assets/js/ai-chat.js')) }}" defer></script>
