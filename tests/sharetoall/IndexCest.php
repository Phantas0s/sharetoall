<?php

namespace sharetoall;

class IndexCest
{
    public function modalLogin(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-login');
        $I->seeElement('#modal-login');
    }

    public function modalRegister(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-register');
        $I->seeElement('#modal-register');
    }

    public function connection(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-login');
        $I->fillField('loginEmail', 'user@sharetoall.com');
        $I->fillField('loginPass','password');
        $I->click('#submit-login');
        $I->wait(4);
        $I->seeInCurrentUrl('/dashboard');
    }
}
