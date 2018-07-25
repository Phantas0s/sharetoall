<?php

namespace sharetoall;

use Codeception\Step\Argument\PasswordArgument;
use SharetoallTester;

class DashboardCest
{
    public function connect(SharetoallTester $I)
    {
        $I->amOnPage('/');
        $I->click('.cc-dismiss');
        $I->wait(2);
        $I->click('#button-login');
        $I->fillField('[name = loginEmail]', 'user@sharetoall.com');
        $I->fillField('[name = loginPass]', 'password');
        $I->click('#submit-login');
        $I->wait(6);
    }

    public function connectOnLinkedin(SharetoallTester $I)
    {
        $I->seeInCurrentUrl('/dashboard');
        $I->click('.v-btn[data-slug="linkedin"]');
        $I->wait(10);
        $I->makeScreenshot('linkedin');
        $I->fillField('[name = session_key]', 'caillebuster1@hotmail.com');
        $I->fillField('[name = session_password]', new PasswordArgument('t7xx8urjLQXpn6gxbSr0'));
        $I->click('.btn-signin');
        $I->wait(10);
        $I->seeInCurrentUrl('/dashboard');
        $I->makeScreenshot('linkedin_connected');
        $I->seeElement('.connected .v-btn[data-slug="linkedin"]');
        $I->click('.v-btn[data-slug="linkedin"]');
    }

    public function connectOnTwitter(SharetoallTester $I)
    {
        $I->seeInCurrentUrl('/dashboard');
        $I->click('.v-btn[data-slug="twitter"]');
        $I->wait(6);
        $I->makeScreenshot('twitter');
        $I->fillField('#username_or_email', new PasswordArgument('MatthieuCneude'));
        $I->fillField('#password', new PasswordArgument('2qFFoT3JFW6u6L6jXAgM'));
        $I->click('#allow');
        $I->wait(10);
        $I->seeInCurrentUrl('/dashboard');
        $I->makeScreenshot('twitter_connected');
        $I->seeElement('.connected .v-btn[data-slug="twitter"]');
        $I->click('.v-btn[data-slug="twitter"]');
    }

    public function Logout(SharetoallTester $I)
    {
        $I->amOnPage('/sharetoall#/dashboard');
        $I->click('.cc-dismiss');
        $I->wait(2);
        $I->click('#button-logout');
        $I->wait(1);
        $I->amOnPage('/');
    }

    public function sendMessageToLinkedin(SharetoallTester $I)
    {
        // $I->click('.btn[data-slug="linkedin"]');
        // $I->fillField('#message', 'Hello! Somebody is searching a job?');
        // $I->click('#share');
        // $I->makeScreenshot('linkedin_mess_send');
        // $I->wait(10);
        // $I->makeScreenshot('linkedin_mess_sent');
        // $I->seeElement('success');
    }
}
