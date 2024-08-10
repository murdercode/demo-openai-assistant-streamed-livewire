<?php

namespace App\Livewire;

use Livewire\Component;

use OpenAI\Laravel\Facades\OpenAI;

class AiAssistant extends Component
{
    public $threadId;
    public $inputMessage;

    public function mount() {
        $this->threadId = $this->setThread();
    }

    /**
     * Create a thread where the AI assistant will respond to messages.
     */
    public function setThread(array $parameters = [])
    {
        $thread = OpenAI::threads()->create($parameters);
        return $thread->id;
    }

    /**
     * Send a message to the AI assistant.
     */
    public function sendMessage(): static
    {
        OpenAI::threads()->messages()->create($this->threadId, [
            'role' => 'user',
            'content' => $this->inputMessage,
        ]);

        $this->streamAiResponse($this->inputMessage);

        return $this;
    }

    /** 
     * Stream the AI assistant's response to the user's message. 
     * */
    public function streamAiResponse($message)
    {
       $stream = \OpenAI\Laravel\Facades\OpenAI::threads()->runs()->createStreamed(
            threadId: $this->threadId,
            parameters: [
            'assistant_id' => config('openai_assistant.id'),
        ]);

        $streamResponse = '';

        foreach($stream as $response){
            if($response->event == 'thread.message.delta') {
                $this->stream(to: 'answer', content: $response->response->delta->content[0]->text->value);
                $streamResponse .= $response->response->delta->content[0]->text->value;
            }

            if($response->event == 'thread.run.completed') {
                $this->messages[] = [
                        'role' => 'assistant',
                        'content' => $streamResponse,
                    ];

            }
        }
    }

    public function render()
    {
        return view('livewire.ai-assistant');
    }
}
