<?php

namespace sharetoall;

class AboutCest
{
    public function AboutPage(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-about');
        $I->wait(4);
        $I->seeElement('#about-container');
    }
}
