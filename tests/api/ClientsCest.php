<?php


class ClientsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }

    public function testList(ApiTester $I)
    {
        $I->sendGET('/?route=clients/list');
        $I->seeResponseContainsJson([
                'status' => [
                        'code' => 403,
                ]
        ]);
        $I->sendPOST('/?route=/user/login', [
                'user' => 'admin@opake.com',
                'pwd' => 'opake2015'
        ]);
        $I->sendGET('/?route=clients/list');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationid');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationname');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.nuanceorgid');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationdetails.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationdetails.address');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationdetails.country');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.organizationdetails.website');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.administratorinfo.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.administratorinfo.phone');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.administratorinfo.email');
        $I->seeResponseJsonMatchesJsonPath('$.data.clients.*.permissions.settings');

    }

    public function testLocations(ApiTester $I)
    {
        $I->sendGET('/?route=clients/locations');
        $I->seeResponseJsonMatchesJsonPath('$.data.locations.*.storageid');
        $I->seeResponseJsonMatchesJsonPath('$.data.locations.*.storagename');
    }

    public function testOrgProfile(ApiTester $I)
    {
        $org_id = 18;
        $I->sendGET('/?route=clients/organizationProfile&id='.$org_id);
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationid');
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationname');
        $I->seeResponseJsonMatchesJsonPath('$.data.nuanceorgid');
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationdetails.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationdetails.address');
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationdetails.country');
        $I->seeResponseJsonMatchesJsonPath('$.data.organizationdetails.website');
        $I->seeResponseJsonMatchesJsonPath('$.data.administratorinfo.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.administratorinfo.phone');
        $I->seeResponseJsonMatchesJsonPath('$.data.administratorinfo.email');
        $I->seeResponseJsonMatchesJsonPath('$.data.permissions.settings');

    }
}
