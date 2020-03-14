<?php


class AlertsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }

    public function testMyalerts(ApiTester $I)
    {
        $I->sendGET('/?route=alerts/myalerts');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.alertid');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.phase');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.type');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.viewed');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.title');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.subtitle');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.date');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.object_id');
        $I->seeResponseJsonMatchesJsonPath('$.data.alerts.*.alertdata');
    }

    public function testGetAlert(ApiTester $I)
    {
        $id = 177;
        $I->sendGET('/?route=alerts/alert&alertid='.$id);
        $I->seeResponseJsonMatchesJsonPath('$.data.alertid');
        $I->seeResponseJsonMatchesJsonPath('$.data.phase');
        $I->seeResponseJsonMatchesJsonPath('$.data.type');
        $I->seeResponseJsonMatchesJsonPath('$.data.viewed');
        $I->seeResponseJsonMatchesJsonPath('$.data.title');
        $I->seeResponseJsonMatchesJsonPath('$.data.subtitle');
        $I->seeResponseJsonMatchesJsonPath('$.data.date');
        $I->seeResponseJsonMatchesJsonPath('$.data.object_id');
        $I->seeResponseJsonMatchesJsonPath('$.data.alertdata');
    }

    public function testChangingPhase(ApiTester $I)
    {
        $id = 177;
        $I->sendGET('/?route=alerts/changePhase&alertid='.$id.'&alertPhase=' . Opake\Model\Alert\Alert::PHASE_ACTION_TAKEN);
        $I->seeResponseContainsJson([
                'data' => 'ok'
        ]);
        $I->sendGET('/?route=alerts/changePhase&alertid='.$id.'&alertPhase=' . Opake\Model\Alert\Alert::PHASE_RESOLVED);
        $I->seeResponseContainsJson([
                'data' => 'ok'
        ]);
        $I->sendGET('/?route=alerts/changePhase&alertid='.$id.'&alertPhase=' . Opake\Model\Alert\Alert::PHASE_REQUIRES_ACTION);
        $I->seeResponseContainsJson([
                'data' => 'ok'
        ]);

        $I->sendGET('/?route=alerts/changePhase&alertid='.$id.'&alertPhase=3');
        $I->seeResponseContainsJson([
                'status' => [
                        'code' => 400,
                        'suggestion' => 'Unknown phase'
                ]
        ]);
    }

    public function testDeleteAlert(ApiTester $I)
    {
        $id = 177;
        $I->sendGET('/?route=alerts/delete&alertid='.$id);
        $I->seeResponseContainsJson([
                'data' => 'ok'
        ]);
    }

}
