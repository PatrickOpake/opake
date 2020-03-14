<?php


class VendorsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }

    public function testVendorList(ApiTester $I)
    {
        $I->sendGet('/?route=/vendors/vendorslist');
        $I->seeResponseJsonMatchesJsonPath('$.data.vendors.*.vendorid');
        $I->seeResponseJsonMatchesJsonPath('$.data.vendors.*.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.vendors.*.email');
    }

    public function testVendor(ApiTester $I)
    {
        $id = 82;
        $I->sendGet('/?route=/vendors/vendor&id='.$id);
        $I->seeResponseJsonMatchesJsonPath('$.data.vendorid');
        $I->seeResponseJsonMatchesJsonPath('$.data.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.contactname');
        $I->seeResponseJsonMatchesJsonPath('$.data.contactphone');
        $I->seeResponseJsonMatchesJsonPath('$.data.contactemail');
        $I->seeResponseJsonMatchesJsonPath('$.data.website');
        $I->seeResponseJsonMatchesJsonPath('$.data.address');
        $I->seeResponseJsonMatchesJsonPath('$.data.country');
        $I->seeResponseJsonMatchesJsonPath('$.data.phone');
        $I->seeResponseJsonMatchesJsonPath('$.data.email');
        $I->seeResponseJsonMatchesJsonPath('$.data.other');
        $I->seeResponseJsonMatchesJsonPath('$.data.mmi');

    }
}
