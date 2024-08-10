<?php

namespace App\Console\Commands;

use App\Models\Fruit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use OpenAI\Laravel\Facades\OpenAI;

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
        $fileName = 'fruits.json';

        $this->info('Converting your fruits to JSON...');
        $fruits = Fruit::all()->toJson();

        $this->info('Saving fruits to fruits.json...');
        Storage::disk('local')->put($fileName, $fruits);

        $this->warn('Educating the AI assistant...');
        $assistant = OpenAI::assistants()->retrieve(config('openai_assistant.id'));

        $this->warn('Deleting existing vector stores and files...');
        if(isset($assistant->toolResources->fileSearch->vectorStoreIds))
        {
            foreach($assistant->toolResources->fileSearch->vectorStoreIds as $vectorStoreId) {
                OpenAI::vectorStores()->delete($vectorStoreId);
            }
        }
        $files = OpenAI::files()->list(['purpose' => 'assistants']);
        foreach($files->data as $file) {
            if($file->filename === $fileName) {
                OpenAI::files()->delete($file->id);
            }
        }

        $this->warn('Uploading fruits.json to OpenAI...');

        $file = OpenAI::files()->upload([
            'purpose' => 'assistants',
            'file' => fopen(Storage::disk('local')->path($fileName), 'rb'),
        ]);

        $this->warn('Creating a new vector store...');
        $vectorStore = OpenAI::vectorStores()->create([
            'file_ids' => [
                $file->id,
            ],
            'name' => 'fruits.vector.json',
        ]);

        $this->warn('Modifying the AI assistant...');
        $response = OpenAI::assistants()->modify(config('openai_assistant.id'), [
            'tool_resources' => [
                'file_search' => [
                    'vector_store_ids' => [
                        $vectorStore->id,
                    ],
                ],
            ],
        ]);

        $this->info('AI assistant has been educated!');

        return 0;
    }
}
