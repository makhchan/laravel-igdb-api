<div class="game mt-8">
    <div class="relative inline-block">
        <a href="{{route('games.show', ['slug'=>$game['slug']])}}">
            <img src="{{$game['coverImageUrl']}}" alt="Game Cover"
                 class="hover:opacity-75 transition ease-in-out duration-150 shadow-md">
        </a>
        @if(!is_null($game['rating']))
            <div class="absolute bottom-0 right-0 w-16 h-16 bg-gray-800 rounded-full"
                 style="right: -20px;bottom: -20px">
                <div id="{{$game['slug']}}" class="font-semibold text-xs flex justify-center items-center h-full relative">
                    @push('scripts')
                        @include('partials._rating', [
                            'slug' => $game['slug'],
                            'rating' => $game['rating'],
                            'event' => null
                        ])
                    @endpush
                </div>
            </div>
        @endif
    </div>
    <a href="{{route('games.show', ['slug'=>$game['slug']])}}" class="block text-base font-semibold leading-tight hover:text-gray-400 mt-8">
        {{$game['name']}}
    </a>
    <div class="text-gray-400 mt-1">
        {{$game['platforms']}}
    </div>
</div>
