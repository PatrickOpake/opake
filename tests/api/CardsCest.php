<?php


class CardsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }

    protected function checkStaffStructure(ApiTester $I)
    {
        $I->seeResponseJsonMatchesJsonPath('$.data.cardid');
        $I->seeResponseJsonMatchesJsonPath('$.data.cardname');
        $I->seeResponseJsonMatchesJsonPath('$.data.cardstate');
        $I->seeResponseJsonMatchesJsonPath('$.data.userid');
        $I->seeResponseJsonMatchesJsonPath('$.data.username');
        $I->seeResponseJsonMatchesJsonPath('$.data.userjobname');
        $I->seeResponseJsonMatchesJsonPath('$.data.userphotourl');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemname');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemtype');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemdesc');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemimageurl');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.qty');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.status');
        $I->seeResponseJsonMatchesJsonPath('$.data.notes.*.text');
        $I->seeResponseJsonMatchesJsonPath('$.data.notes.*.status');
    }

    protected function checkLocationStructure(ApiTester $I)
    {
        $I->seeResponseJsonMatchesJsonPath('$.data.cardid');
        $I->seeResponseJsonMatchesJsonPath('$.data.cardtype');
        $I->seeResponseJsonMatchesJsonPath('$.data.cardname');
        $I->seeResponseJsonMatchesJsonPath('$.data.cardstate');
        $I->seeResponseJsonMatchesJsonPath('$.data.locationid');
        $I->seeResponseJsonMatchesJsonPath('$.data.locationname');

        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemname');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemtype');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemdesc');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itemimageurl');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.qty');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.status');
        $I->seeResponseJsonMatchesJsonPath('$.data.notes.*.text');
        $I->seeResponseJsonMatchesJsonPath('$.data.notes.*.status');
    }

    public function testGetStaff(ApiTester $I)
    {
        $caseid = 1491;
        $userid = 5;
        $cardid = 85;
        $I->sendGET('/?route=cards/staff&caseid='. $caseid .'&userid='.$userid);
        $this->checkStaffStructure($I);
        $I->sendGET('/?route=cards/staff&cardid='.$cardid);
        $this->checkStaffStructure($I);
    }

    public function testGetLocation(ApiTester $I)
    {
        $caseid = 1491;
        $cardtype = 0;
        $cardid = 67;

        $I->sendGET('/?route=cards/location&caseid='. $caseid .'&cardtype='.$cardtype);
        $this->checkLocationStructure($I);
        $I->sendGET('/?route=cards/location&cardid='.$cardid);
        $this->checkLocationStructure($I);
    }
}
