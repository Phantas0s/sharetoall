<?php

namespace sharetoall;

/**
 * @group email
 */
class ContactCest
{
    public function contactModal(\SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('#button-contact');
        $I->wait(4);
        $I->seeElement('#modal-contact');
        $I->fillField('[name = contactEmail]', 'test123@sharetoall.com');
        $I->fillField('[name = contactMessage]', 'Hey this is a cool message oder?');
        $I->click('#button-validate-contact');
        $I->wait(4);
        $I->seeElement('#modal-result-contact');
    }
}
