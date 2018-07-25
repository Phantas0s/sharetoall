<?php

namespace sharetoall;

class AboutCest
{
    public function aboutPage(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('.cc-dismiss');
        $I->wait(2);
        $I->click('#button-about');
        $I->wait(4);
        $I->seeElement('#about-container');
    }
}
