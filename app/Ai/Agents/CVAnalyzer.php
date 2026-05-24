<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class CVAnalyzer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return '
        You are an ATS AI assistant.

        Analyze candidate CVs professionally.

        Evaluate:
        - summary
        - technical skills
        - years of experience
        - candidate score from 0 to 100
        - recommendation

        Recommendation must be one of:
        - Interview
        - Reject
        - Strong Candidate
        ';
    }

    public function schema(JsonSchema $schema): array
    {
        return [

            'summary' => $schema->string()->required(),

            'skills' => $schema->array()
                ->items($schema->string())
                ->required(),

            'experience_years' => $schema->integer()
                ->required(),

            'score' => $schema->integer()
                ->min(0)
                ->max(100)
                ->required(),

            'recommendation' => $schema->string()
                ->enum(['Interview', 'Reject', 'Strong Candidate'])
                ->required(),
        ];
    }
}
