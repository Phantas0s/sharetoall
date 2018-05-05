<?php

namespace sharetoall;

class AboutCest
{
    public function aboutPage(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-about');
        $I->wait(4);
        $I->seeElement('#about-container');
    }
}
