<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\Weather;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class EventWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Event $event)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $eventDateTime = Carbon::parse($this->event->date.' '.$this->event->time);
        $path = config('openweathermap.open_weather_map_url'); // path to third api
        // prepare our http query builder
        $queryParams = http_build_query(
            [
                'q' => $this->event->location,
                'dt' => $eventDateTime->timestamp,
                'appid' => config('openweathermap.open_weather_map_key'), // Get our api key
            ]
        );

        try {
            // Send a request to the third-party API
            $response = Http::get($path, $queryParams);

            // Check if the response was successful
            if ($response->successful()) {
                $data = $response->json();

                // Store the weather information
                $this->event->weather()->save(new Weather([
                    'temperature' => $data['main']['temp'],
                    'description' => $data['weather'][0]['description'],
                    'main' => $data['weather'][0]['main'],
                    'icon' => $data['weather'][0]['icon'],
                ]));
            } else {
                // Failed response, throw an exception
                $response->throw();
            }
        } catch (\Exception $e) {
            // force fail the job
            $this->fail('Job failed: '.$e->getMessage());
        }

    }
}
