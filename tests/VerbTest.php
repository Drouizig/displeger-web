<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;

class VerbTest extends PantherTestCase
{
    public function testPdfExport(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/br/verb/debriñ');
        
        $this->assertPageTitleContains('debriñ');

        $link = $crawler->filter('.export-button')->link();
        $client->click($link);

        $response = $client->getInternalResponse();
        
        $this->assertSame($response->getHeader('Content-Type'), 'application/pdf');
        $this->assertPageTitleContains('debriñ');
    }


    /** Test d1: debriñ */
    public function textRegularVerb() : void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/br/verb/debriñ');
        $this->assertPageTitleContains('debriñ');
        $this->assertSelectorTextContains('.verb-items', 'debran');


    }
}