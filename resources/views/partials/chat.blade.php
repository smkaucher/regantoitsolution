<div class="chat" id="chat">
    <div class="chat-panel" id="chatPanel" role="dialog" aria-label="Chat with our team" hidden>
        <b>Fast Reply</b>
        <p>Choose an option to chat with our team.</p>
        <a href="{{ $site['facebook'] ?? '#' }}" target="_blank" rel="noreferrer"><b>Messenger</b> {{ $site['site_name'] ?? '' }}<small>Typically replies in ~30 minutes</small></a>
        <a href="{{ $site['whatsapp'] ?? '#' }}" target="_blank" rel="noreferrer"><b>WhatsApp</b> {{ $site['phone'] ?? '' }}</a>
    </div>
    <button class="chat-fab" id="chatFab" type="button" aria-label="Open live chat" aria-expanded="false">
        <span class="chat-fab-ico"><span id="chatIco">💬</span></span>
        <span class="chat-fab-txt"><b>Live Chat</b><small>WhatsApp / Messenger</small></span>
    </button>
</div>
