<div class="chat" id="chat">
    <div class="chat-panel" id="chatPanel" role="dialog" aria-label="Chat with our team" hidden>
        <b>Fast Reply</b>
        <p>Choose an option to chat with us.</p>
        <a href="#" data-ai-open onclick="document.getElementById('chatPanel').hidden=true;document.getElementById('chatFab').setAttribute('aria-expanded','false')"><b>Reganto AI</b> Instant answers, 24/7<small>Ask about services, projects and booking</small></a>
        <a href="{{ $site['facebook'] ?? '#' }}" target="_blank" rel="noreferrer"><b>Messenger</b> {{ $site['site_name'] ?? '' }}<small>Typically replies in ~30 minutes</small></a>
        <a href="{{ $site['whatsapp'] ?? '#' }}" target="_blank" rel="noreferrer"><b>WhatsApp</b> {{ $site['phone'] ?? '' }}</a>
    </div>
    <button class="chat-fab" id="chatFab" type="button" aria-label="Open live chat" aria-expanded="false">
        <span class="chat-fab-ico"><span id="chatIco">💬</span></span>
        <span class="chat-fab-txt"><b>Live Chat</b><small>AI · WhatsApp · Messenger</small></span>
    </button>
</div>
