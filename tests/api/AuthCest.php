<?php


class AuthCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function testAuthIsNotLogged(ApiTester $I)
    {
        $I->wantTo('check user auth is not logged');
        $I->sendGET('/');
        $I->seeResponseContainsJson(['status' => [
            'code' => 401,
            'message' => 'Unauthorized'
        ]]);
    }


    public function testLogged(ApiTester $I)
    {
        $I->wantTo('try to log in and log out');
        $I->sendPOST('/?route=/user/login', [
            'user' => 'api-test-admin@example.com',
            'pwd' => 'password'
        ]);
        $I->seeResponseContainsJson(['data' => [
            'email' => 'api-test-admin@example.com',
            'firstname' => 'ApiTest',
            'lastname' => 'Admin',
            'accesslevel' => 1,
        ]]);
        $I->seeResponseJsonMatchesJsonPath('$.data.organization.nuanceorgid');
        $I->sendPOST('/?route=/user/logout');
        $I->sendGET('/');
        $I->seeResponseContainsJson(['status' => [
            'code' => 401,
            'message' => 'Unauthorized'
        ]]);
    }
}
