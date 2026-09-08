<?php
    namespace App\Services;

use Anthropic\Client;

    class ClaudeService
    {
        public function __construct(public Client $client){}
        public function ask(string $prompt,?string $system=null):string
        {
            $params=[
                'maxTokens'=>1024,
                'messages'=>[
                    [
                        'role'=>'user',
                        'content'=>$prompt,
                    ]
                ],
                'model'=>config('services.anthropic.model')
            ];
            if($system !==null)
                $params['system']=$system;

            $message = $this->client->messages->create(...$params);
            return $this->extractText($message);
        }
        public function extractText(object $message): string
        {
            $parts=[];
            foreach($message->content as $block)
            {
                if($block->type==="text")
                    $parts[]=$block->text;
            }
            return implode("\n",$parts);
        }
    }