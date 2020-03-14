<?php


class ReportsCest extends BaseApiCest
{
    public function _before(ApiTester $I)
    {
        $this->loginAsAdmin($I);
    }


    public function testMyReports(ApiTester $I)
    {
        $I->sendPOST('/?route=/user/login', [
                'user' => 'gold@opake.com',
                'pwd' => 'opake2015'
        ]);

        $I->sendGET('/?route=/reports/myreports');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.firstname');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.lastname');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.status');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.caseid');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.type');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.typeid');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.typecode');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.provider');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.reportid');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.reportstatus');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.patient');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.admittype');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.locationid');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.locationname');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.datestart');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.datefinish');
        $I->seeResponseJsonMatchesJsonPath('$.data.reports.*.staff');
    }

    public function testChangeStatus(ApiTester $I)
    {
        $reportid = 5;
        $I->sendPOST('/?route=/user/login', [
                'user' => 'gold@opake.com',
                'pwd' => 'opake2015'
        ]);

        $I->sendGet('/?route=/reports/changestatus&status=' . Opake\Model\Cases\OperativeReport::STATUS_ARCHIVE.'&'.'reportid='.$reportid);
        $I->seeResponseContainsJson([
                'data' => [
                        'success' => true,
                ]
        ]);

        $I->sendGet('/?route=/reports/changestatus&status=' . Opake\Model\Cases\OperativeReport::STATUS_SUBMITTED.'&'.'reportid='.$reportid);
        $I->seeResponseContainsJson([
                'data' => [
                        'success' => true,
                ]
        ]);

        $I->sendGet('/?route=/reports/changestatus&status=' . Opake\Model\Cases\OperativeReport::STATUS_DRAFT.'&'.'reportid='.$reportid);
        $I->seeResponseContainsJson([
                'data' => [
                        'success' => true,
                ]
        ]);

        $I->sendGet('/?route=/reports/changestatus&status=' . Opake\Model\Cases\OperativeReport::STATUS_OPEN.'&'.'reportid='.$reportid);
        $I->seeResponseContainsJson([
                'data' => [
                        'success' => true,
                ]
        ]);

        $I->sendGet('/?route=/reports/changestatus&status=6&reportid='.$reportid);
        $I->seeResponseContainsJson([
                'status' => [
                        'code' => 400,
                ]
        ]);

    }

    public function testGetTemplates(ApiTester $I)
    {
        $caseid = 1517;
        $I->sendGet('/?route=/reports/templates&caseid='.$caseid);
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.organization_id');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.name');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.anesthesia_administered');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.ebl');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.drains');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.consent');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.complications');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.approach');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.description_procedure');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.follow_up_care');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.conditions_for_discharge');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.specimens_removed');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.findings');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.urine_output');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.fluids');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.blood_transfused');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.total_tourniquet_time');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.clinical_history');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.updated');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.case_type');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.custom_fields');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.template.fields');
        $I->seeResponseJsonMatchesJsonPath('$.data.templates.*.template.custom_fields');
    }
}
