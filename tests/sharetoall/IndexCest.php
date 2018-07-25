<?php

namespace sharetoall;

class IndexCest
{
    public function modalLogin(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('.cc-dismiss');
        $I->wait(2);
        $I->click('#button-login');
        $I->seeElement('#modal-login');
    }

    public function modalRegister(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('.cc-dismiss');
        $I->wait(2);
        $I->click('#button-register');
        $I->seeElement('#modal-register');
    }
}
