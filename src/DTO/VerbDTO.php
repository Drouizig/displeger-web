<?php

namespace App\DTO;

class VerbDTO
{

    public function __construct(
        public readonly string $infinitive,
        public readonly string $previousVerb,
        public readonly string $nextVerb,
        public readonly array $translations,
        public readonly array $tenses,
        public readonly array $tags,
        public readonly array $organisation,
    )
    {
    }

}