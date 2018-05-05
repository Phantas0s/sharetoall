<?php

namespace sharetoall;

/**
 * @group email
 */
class RegistrationCest
{
    public function modalRegister(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-register');
        $I->seeElement('#modal-register');
        $I->fillField('[name = userEmail]', 'test123@sharetoall.com');
        $I->fillField('[name = userPassword]', 'password');
        $I->fillField('[name = userPasswordConfirm]', 'password');
        $I->click('[name = userNewsletter]');
        $I->click('#button-validate-register');
        $I->wait(4);
        $I->seeElement('#popup-result-register');
    }
}
