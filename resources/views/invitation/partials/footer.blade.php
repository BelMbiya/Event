    <!-- Footer -->
    <footer class="footer-section py-5" style="background-image: url('{{ asset('img/invitations/sections/'.$content->footer_background_image) }}'); background-size: cover; background-position: center; background-attachment: fixed; position: relative;">
        <!-- Overlay pour la lisibilité -->
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.3); z-index: 1;"></div>
        <div class="container text-center" style="position: relative; z-index: 2;">
            <p class="romantic-font text-white mb-2" style="font-size: 2rem;">{!! $content->couple ?? 'Notre Mariage' !!}</p>
            @if($content->event_datetime)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($content->event_datetime)->locale('fr')->translatedFormat('d F Y') }}</p>
            @elseif($event->event_date)
            <p class="elegant-font text-white-50">{{ \Carbon\Carbon::parse($event->event_date)->locale('fr')->translatedFormat('d F Y') }}</p>
            @endif
        </div>
    </footer>
