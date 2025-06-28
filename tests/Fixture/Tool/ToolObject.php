<?php

declare(strict_types=1);

namespace PhpLlm\LlmChain\Tests\Fixture\Tool;

use PhpLlm\LlmChain\Chain\Toolbox\Attribute\AsTool;

#[AsTool('tool_object', 'A tool with object parameter')]
final class ToolObject
{
    public function __invoke(ParameterDto $object): string
    {
        return 'Success!';
    }
}

class ParameterDto
{
    /** @var int Good int */
    public int $number;
}
