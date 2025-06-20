<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Platform\Bridge\HuggingFace;

use PhpLlm\LlmChain\Platform\Model;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class ApiClient
{
    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    /**
     * @return Model[]
     */
    public function models(?string $provider, ?string $task): array
    {
        $response = $this->httpClient->request('GET', 'https://huggingface.co/api/models', [
            'query' => [
                'inference_provider' => $provider,
                'pipeline_tag' => $task,
            ],
        ]);

        return array_map(fn (array $model) => new Model($model['id']), $response->toArray());
    }
}
