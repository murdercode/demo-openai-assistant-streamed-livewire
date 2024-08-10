<?php

namespace App\Console\Commands;

use App\Models\Fruit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AiAssistantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-assistant:educate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Converting your fruits to JSON...');
        $fruits = Fruit::all()->toJson(JSON_PRETTY_PRINT);

        $this->info('Saving fruits to fruits.json...');
        Storage::disk('local')->put('fruits.json', $fruits);


    }
}
