<?php

namespace Web;

class HomeCest
{
    // Execute before running each test
    public function _before(\WebTester $I)
    {
    }

    // Execute after running each test
    public function _after(\WebTester $I)
    {
    }

    public function loginLicensorOwner(\WebTester $I)
    {
        $I->amOnPage('/');
        $I->see('PANTAFLIX PRO');
    }
}