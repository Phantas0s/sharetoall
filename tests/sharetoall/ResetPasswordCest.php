<?php

namespace sharetoall;

/**
 * @group email
 */
class ResetPasswordCest
{
    public function modalResetPassword(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-login');
        $I->seeElement('#modal-login');
        $I->click('#button-reset-password');
        $I->wait(4);
        $I->seeElement('#modal-reset-password');
        $I->fillField('[name = resetPasswordEmail]', 'user@sharetoall.com');
        $I->click('#button-validate-reset-password');
        $I->wait(4);
        $I->seeElement('#modal-result-reset-password');
    }
}
