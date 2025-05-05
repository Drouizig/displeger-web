<?php

namespace App\Transformer;

use App\DTO\VerbDTO;
use App\Entity\TagTranslation;
use App\Entity\VerbLocalization;
use App\Entity\VerbTag;
use App\Entity\VerbTranslation;

class VerbTransformer
{

    public function transform(VerbLocalization $verbLocalization, string $previousVerb, string $nextVerb, array $verbEndings, array $organisation): VerbDTO
    {
        return new VerbDTO(
            infinitive: $verbLocalization->getInfinitive(),
            previousVerb: $previousVerb,
            nextVerb: $nextVerb,
            translations: array_reduce($verbLocalization->getVerb()->getTranslations()->toArray(),
                function(array $result, VerbTranslation $translation) {
                    $result[$translation->getLanguageCode()] = $translation->getTranslation();
                    return $result;
                }, []),
            tenses: $verbEndings,
            tags: array_reduce(
                $verbLocalization->getVerb()->getTags()->toArray(),
                function(array $result, VerbTag $tag) {
                    $result[$tag->getTag()->getCode()] =
                        array_reduce(
                            $tag->getTag()->getTranslations()->toArray(),
                            function (array $resultTag, TagTranslation $tagTranslation) {
                                $resultTag[$tagTranslation->getLanguageCode()] = $tagTranslation->getLabel();
                                return $resultTag;
                            }, []);
                    return $result;
                }, []
            ),
            organisation: $organisation
        );
    }
}