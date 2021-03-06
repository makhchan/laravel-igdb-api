<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class RecentlyReviewed extends Component
{
    public $recentlyReviewed = [];

    public function loadRecentlyReviewed()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;
        $recentlyReviewedUnformatted = Http::withHeaders([
            'Client-ID' => config('services.igdb.client-id'),
            'Authorization' => config('services.igdb.access-token'),
        ])
            ->withBody(
                "
                    fields name, cover.url, first_release_date, platforms.abbreviation, rating, rating_count, summary, slug;
                    where platforms = (48,49,130,6)
                    & (first_release_date >= {$before}
                    & first_release_date < {$current}
                    & rating_count > 5);
                    sort rating desc;
                    limit 3;
                ", "text/plain"
            )->post(config('services.igdb.api-url'))
            ->json();

        $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);

        collect($this->recentlyReviewed)->filter(function ($game) {
            return $game['rating'];
        })->each(function ($game) {
            $this->emit('reviewGameWithRatingAdded', [
                'slug' => 'review_'.$game['slug'],
                'rating' => $game['rating'] / 100
            ]);
        });
    }
    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    private function formatForView($games){
        return collect($games)->map(function ($game){
            return collect($game)->merge([
                'coverImageUrl' => array_key_exists('cover', $game)?str_replace("thumb", "cover_big", $game['cover']['url']):asset('img/ff7.jpg'),
                'rating' => array_key_exists('rating', $game)?round($game['rating']):null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }
}
