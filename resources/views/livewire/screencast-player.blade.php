<div
    x-data="playerComponent()"
    x-init="init()"
    class="relative p-2 md:p-8 pt-0 bg-gray-100 h-full"
>
    <div class="container mx-auto px-4 lg:px-12 pt-4 text-xl flex-col md:flex-row flex justify-between relative items-center" style="max-width: 900px">
        <div class="pb-16 pt-6 w-full" x-data>
            <div class="rounded overflow-hidden shadow-lg">
                <div style="padding:56.25% 0 0 0;position:relative;" class="relative">
                    @if (! $screencast->is_paid || (auth()->user() && auth()->user()->is_sponsor))
                        <iframe src="{{ base64_decode($screencast->url) }}?autoplay=1" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    @else
                        <div class="absolute inset-0 bg-indigo-100 overflow-hidden">
                            <div class="absolute inset-0 z-0 overflow-hidden" style="
                                transform: scale(1.05);
                                background-image: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.3)), url('/img/screencast_freeze_frame.png');
                                filter: blur(4px);
                                -webkit-filter: blur(4px);
                                background-position: center;
                                background-repeat: no-repeat;
                                background-size: cover;
                            "></div>
                            <div class="flex flex-col justify-center items-center relative z-10 h-full w-full">
                                <div class="font-bold md:mb-8 lg:text-3xl text-center text-white">This video is restricted to GitHub sponsors only.</div>
                                <div class="hidden lg:block lg:mb-8 text-center text-white lg:text-xl">Your sponsorship helps make videos like these possible! ❤️</div>
                                @guest
                                    <div class="mt-4">
                                        <a class="text-sm lg:text-base cursor-pointer border-2 border-white flex hover:border-gray-300 hover:text-gray-400 items-center px-4 md:px-8 py-1 md:py-2 rounded-full shadow text-white" href="/login/github">
                                            <span class="mr-2">Log in with GitHub</span>
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <a class="text-sm md:text-base cursor-pointer border-2 border-white flex hover:border-gray-300 hover:text-gray-400 items-center px-4 md:px-8 py-1 md:py-2 rounded-full shadow text-white" href="https://github.com/sponsors/calebporzio" target="__blank">
                                            <span class="mr-4">Become A GitHub Sponsor</span>
                                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                                        </a>
                                    </div>
                                    <div class="mt-2">
                                        <a class="text-sm text-gray-400 hover:text-gray-300" href="mailto:support@laravel-livewire.com" target="__blank">
                                            Need help? Email support@laravel-livewire.com
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
                <div class="p-4 bg-white">
                    <span class="block border-b font-bold font-display mb-3 text-2xl">{{ $screencast->title }}</span>
                    <span class="block leading-5 text-sm tracking-normal">{{ $screencast->description }}</span>

                    @livewire('explore-code', ['screencast' => $screencast])
                </div>
            </div>
        </div>
    </div>

    <div class="hidden lg:block absolute bottom-0 right-0 pr-4">
        <img class="w-24 pt-8" src="/img/screencast-head.png" alt="Building Livewire Podcast Logo">
    </div>
</div>

<script src="https://player.vimeo.com/api/player.js"></script>
<script>
    const playerComponent = function () {
        return {
            init() {
                var iframe = document.querySelector('iframe');

                if (!iframe) {
                    return;
                }

                var player = new Vimeo.Player(iframe);

                if (localStorage.getItem('livewire.screencasts.rate')) {
                    player.setPlaybackRate(localStorage.getItem('livewire.screencasts.rate'))
                }

                @if (auth()->check() && $screencastProgress && $screencastProgress->last_known_timestamp_in_seconds)
                    player.setCurrentTime({{ $screencastProgress->last_known_timestamp_in_seconds }});
                @endif

                // Automatically send the user to the next video after completion.
                player.on('ended', () => {
                    this.$wire.completed();

                    // Don't the next link if there is none
                    if (@json(! $screencast->next)) return;
                    location.href = '/screencasts/{{ optional($screencast->next)->slug }}';
                });

                // Remember the user's PlaybackRate.
                player.on('playbackratechange', () => {
                    player.getPlaybackRate().then(function (rate) {
                        localStorage.setItem('livewire.screencasts.rate', rate)
                    })
                })

                @auth
                    setInterval(async () => {
                        if (await player.getPaused()) {
                            return;
                        }

                        let duration = await player.getDuration();
                        let seconds = await player.getCurrentTime();

                        if (duration - seconds < 5) {
                            this.$wire.completed();
                        } else {
                            this.$wire.updateLastKnownTimestamp(Math.floor(seconds));
                        }
                    }, 5000);
                @endauth
            }
        }
    };
</script>
