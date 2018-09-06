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
        $I->wait(6);
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

    public function shareOnTwitter(SharetoallTester $I)
    {
        $I->seeInCurrentUrl('/dashboard');
        // Unselect linkedin and dummy network (unit tests)
        $I->click('.v-btn[data-slug="linkedin"]');
        $I->click('.v-btn[data-slug="fakeLinkedin"]');
        $I->click('.v-btn[data-slug="fakeTwitter"]');
        $I->click('.v-btn[data-slug="supernetwork"]');
        // Select twitter
        $I->click('.v-btn[data-slug="twitter"]');
        $I->fillField('#message', $this->randomSentence());
        $I->makeScreenshot('before-twitter-share-message');
        $I->click('#share');
        $I->wait(4);
        $I->makeScreenshot('twitter-share-message');
        $I->seeElement('.success');
        $I->canSee('MatthieuCneude');
    }

    public function shareOnLinkedin(SharetoallTester $I)
    {
        $I->seeInCurrentUrl('/dashboard');
        // Unselect twitter
        $I->click('.v-btn[data-slug="twitter"]');
        // Select linkedin
        $I->click('.v-btn[data-slug="linkedin"]');
        $I->fillField('#message', $this->randomSentence());
        $I->makeScreenshot('before-linkedin-share-message');
        $I->click('#share');
        $I->wait(4);
        $I->makeScreenshot('twitter-linkedin-message');
        $I->seeElement('.success');
        $I->canSee('Jean Dufour');
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

    private function randomSentence():string
    {
        $phrases = array(
            'I like salad which cost' . rand(),
            'Do you like salad which cost ' . rand(),
            'I hope you do like salad which cost ' . rand(),
            'I\'m not sure about my state right now which can cost
            ' . rand(),
        );

        return $phrases[array_rand($phrases)];
    }
}
