<?php

namespace PhpLlm\LlmChain\Tests\Platform\Bridge\Google;

use PhpLlm\LlmChain\Chain\Chain;
use PhpLlm\LlmChain\Chain\Toolbox\ChainProcessor;
use PhpLlm\LlmChain\Chain\Toolbox\Toolbox;
use PhpLlm\LlmChain\Platform\Bridge\Google\Gemini;
use PhpLlm\LlmChain\Platform\Bridge\Google\PlatformFactory;
use PhpLlm\LlmChain\Platform\Message\Message;
use PhpLlm\LlmChain\Platform\Message\MessageBag;
use PhpLlm\LlmChain\Tests\Fixture\Tool\ToolObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(ToolObject::class)]
final class GeminiToolCallDenormalizationFailureTest extends TestCase
{
    #[Test]
    public function testParameterDenormalizationFailure(): void
    {
        $mockHandler = new MockHttpClient([
            new MockResponse(
                <<<'JSON'
                {
                  "candidates": [
                    {
                      "content": {
                        "parts": [
                          {
                            "functionCall": {
                              "id": "call123",
                              "name": "tool_object",
                              "args": {
                                "object": {
                                  "number": 44
                                }
                              }
                            }
                          }
                        ],
                        "role": "model"
                      }
                    }
                  ]
                }
                JSON,
            ),
            new MockResponse(
                <<<'JSON'
                {
                  "candidates": [{
                    "content": {
                      "parts": [{"text": "OK"}],
                      "role": "model"
                    }
                  }]
                }
                JSON,
            ),
        ]);

        $platform = PlatformFactory::create('fake', $mockHandler);
        $llm = new Gemini();

        $toolbox = Toolbox::create(new ToolObject());
        $processor = new ChainProcessor($toolbox);
        $chain = new Chain($platform, $llm, [$processor], [$processor]);

        $messages = new MessageBag(Message::ofUser('What time is it?'));
        $response = $chain->call($messages);

        self::assertSame('OK', $response->getContent());
    }
}
