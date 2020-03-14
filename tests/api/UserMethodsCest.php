<?php


class UserMethodsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {

    }

    public function _after(ApiTester $I)
    {

    }

    // tests
    public function testDetails(ApiTester $I)
    {
        $I->sendPOST('/?route=/user/login', [
            'user' => 'api-test-admin@example.com',
            'pwd' => 'password'
        ]);
        $res = $I->grabJsonResponse()->toArray();

        $id = $res['data']['userid'];
        $I->sendPOST('/?route=/user/details&id=' . $id);
        $I->seeResponseContainsJson([
            'data' => [
                'userid' => $res['data']['userid'],
                'email' => $res['data']['email'],
                'firstname' => $res['data']['firstname'],
                'lastname' => $res['data']['lastname']
            ]
        ]);

    }

    // tests
    public function testPermissions(ApiTester $I)
    {
        $I->sendPOST('/?route=/user/login', [
            'user' => 'api-test-admin@example.com',
            'pwd' => 'password'
        ]);
        $res = $I->grabJsonResponse()->toArray();

        $id = $res['data']['userid'];
        $I->sendPOST('/?route=/user/permissions&id=' . $id);
        $I->seeResponseJsonMatchesJsonPath('$.data.cases');
        $I->seeResponseJsonMatchesJsonPath('$.data.user');
        $I->seeResponseJsonMatchesJsonPath('$.data.card');
        $I->seeResponseJsonMatchesJsonPath('$.data.cases.create');
        $I->seeResponseJsonMatchesJsonPath('$.data.cases.view');
        $I->seeResponseJsonMatchesJsonPath('$.data.cases.edit');
    }
}
