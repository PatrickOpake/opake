<?php


class InventoryCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }

    public function testGetInventory(ApiTester $I)
    {
        $id = 3646;
        $I->sendGET('/?route=inventory/product&id='.$id);
        $I->seeResponseContainsJson([
                'data' => [
                        'id' => $id,
                ]
        ]);
        $I->seeResponseJsonMatchesJsonPath('$.data.type');
        $I->seeResponseJsonMatchesJsonPath('$.data.image');
        $I->seeResponseJsonMatchesJsonPath('$.data.description');
        $I->seeResponseJsonMatchesJsonPath('$.data.parlevel');
        $I->seeResponseJsonMatchesJsonPath('$.data.remanufacturable');
        $I->seeResponseJsonMatchesJsonPath('$.data.resterilizable');
        $I->seeResponseJsonMatchesJsonPath('$.data.itempacks');
        $I->seeResponseJsonMatchesJsonPath('$.data.mmisid');
        $I->seeResponseJsonMatchesJsonPath('$.data.manufacturerid');
        $I->seeResponseJsonMatchesJsonPath('$.data.manufacturername');
    }

    public function testSearchInventory(ApiTester $I)
    {
        $I->sendGET('/?route=inventory/search&offset=0&limit=10');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.id');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.type');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.image');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.description');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.parlevel');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.remanufacturable');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.resterilizable');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.itempackid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.qty');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.expdate');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.locationid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.locationname');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.itempacks.*.distributorid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.mmisid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.manufacturerid');
        $I->seeResponseJsonMatchesJsonPath('$.data.items.*.manufacturername');
    }

    public function testSaveInventory(ApiTester $I)
    {
        $id = 3646;
        $I->sendGET('/?route=inventory/product&id='.$id);
        $res = $I->grabJsonResponse()->toArray();
        $I->sendPOST('/?route=save/product', ['data' => json_encode($res['data'])]);
        $I->seeResponseContainsJson([
                'data' => [
                        'id' => $res['data']['id'],
                ]
        ]);
    }

    public function testTypes(ApiTester $I)
    {
        $I->sendGET('/?route=inventory/types');
        $I->seeResponseJsonMatchesJsonPath('$.data.types.*.name');
    }

    public function testUoms(ApiTester $I)
    {
        $I->sendGET('/?route=inventory/uoms');
        $I->seeResponseJsonMatchesJsonPath('$.data.uoms');
    }


}
