{{-- Reviews: the 3D card deck (home.js initCarousels). On scroll-in the deck fans out from the middle.
     "Write a review" opens a form (home.js initReviewForms); a sent review is saved hidden until an admin shows it. --}}
<section class="block hm hm-rev" data-replay data-threshold="0.2">
    <div class="hm-head">
        <p class="hm-eb"><i></i>[ CLIENT REVIEWS ]</p>
        <h2 class="hm-h2">What clients say about <em>working with us.</em></h2>
        <p class="hm-sub"><b>★ 4.9</b> out of 5 from 30+ reviews. <a href="https://clutch.co/">Read them on Clutch</a></p>
    </div>

    @if($testimonials->count())
    <div class="cf" data-carousel role="region" aria-roledescription="carousel" aria-label="Client reviews" tabindex="0">
        <button type="button" class="cf-arrow prev" data-prev aria-label="Previous review"><x-icon name="chevron" size="18" style="transform:rotate(180deg)" /></button>
        <div class="cf-stage">
            @foreach($testimonials as $t)
                @php $rating = max(1, min(5, (int) $t->rating)); @endphp
                <figure class="cf-card rv-card" data-card>
                    <span class="rv-mark" aria-hidden="true">“</span>
                    <div class="rv-top">
                        <div class="cf-stars rv-stars" aria-label="{{ $rating }} out of 5 stars"><span>{{ str_repeat('★', $rating) }}</span>{{ str_repeat('★', 5 - $rating) }}</div>
                        <span class="rv-score">{{ $rating }}.0</span>
                    </div>
                    <blockquote>{{ $t->quote }}</blockquote>
                    <figcaption>
                        @if($t->photo)
                            <img class="cf-av rv-photo" src="{{ \App\Support\Media::url($t->photo) }}" alt="" width="44" height="44" loading="lazy" decoding="async">
                        @else
                            <i class="cf-av">{{ mb_strtoupper(mb_substr(trim($t->name), 0, 1)) }}</i>
                        @endif
                        <span><b>{{ $t->name }}</b>@if($t->role)<small>{{ $t->role }}</small>@endif</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
        <button type="button" class="cf-arrow next" data-next aria-label="Next review"><x-icon name="chevron" size="18" /></button>
        <div class="cf-dots">
            @foreach($testimonials as $i => $t)
                <button type="button" data-dot="{{ $i }}" aria-label="Show review {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
    @endif

    <div class="rv-cta">
        <p>Worked with us? <span>Tell others how it went.</span></p>
        <button type="button" class="hm-btn" data-review-open>Write a review <span><x-icon name="star" size="16" /></span></button>
    </div>

    <dialog class="rv-dlg" data-review-dialog aria-labelledby="rv-title">
        <form class="rv-form" method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" data-review-form novalidate>
            <button type="button" class="rv-x" data-review-close aria-label="Close">×</button>
            <p class="hm-eb"><i></i>[ YOUR REVIEW ]</p>
            <h3 id="rv-title">Share your experience</h3>
            <p class="rv-note">Your review shows on the site after our team checks it.</p>

            <fieldset class="rv-rate">
                <legend>Your rating *</legend>
                <div class="rv-rate-stars">
                    @foreach([5, 4, 3, 2, 1] as $n)
                        <input type="radio" id="rv-r{{ $n }}" name="rating" value="{{ $n }}" @checked($n === 5)>
                        <label for="rv-r{{ $n }}" title="{{ $n }} out of 5 stars">★</label>
                    @endforeach
                </div>
                <em data-err="rating"></em>
            </fieldset>

            <div class="rv-row">
                <label>Your name *<input type="text" name="name" maxlength="120" autocomplete="name" required><em data-err="name"></em></label>
                <label>Role / company<input type="text" name="role" maxlength="150" placeholder="e.g. Owner, ABC Fashion" autocomplete="organization-title"><em data-err="role"></em></label>
            </div>
            <label>Your review *<textarea name="quote" rows="4" minlength="10" maxlength="600" placeholder="How was working with us?" required></textarea><small class="rv-count"><b data-review-count>0</b>/600</small><em data-err="quote"></em></label>
            <label class="rv-file">Your photo (optional)
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
                <small>JPG, PNG or WebP, up to 2 MB.</small>
                <em data-err="photo"></em>
            </label>
            {{-- Bots fill this; people never see it. --}}
            <input class="rv-hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" hidden>

            <p class="rv-status" data-review-status role="status" aria-live="polite"></p>
            <button type="submit" class="hm-btn rv-send">Send review <span><x-icon name="send" size="16" /></span></button>
        </form>
    </dialog>
</section>
