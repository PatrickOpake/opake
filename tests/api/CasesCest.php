<?php

class CasesCest extends BaseApiCest
{
	public function _before(ApiTester $I)
	{
		$this->loginAsAdmin($I);
	}

	public function _after(ApiTester $I)
	{

	}

	protected function checkJSONStructure(ApiTester $I)
	{
		$I->seeResponseJsonMatchesJsonPath('$.data.caseid');
		$I->seeResponseJsonMatchesJsonPath('$.data.type');
		$I->seeResponseJsonMatchesJsonPath('$.data.typeid');
		$I->seeResponseJsonMatchesJsonPath('$.data.typecode');
		$I->seeResponseJsonMatchesJsonPath('$.data.provider');
		$I->seeResponseJsonMatchesJsonPath('$.data.reportid');
		$I->seeResponseJsonMatchesJsonPath('$.data.reportstatus');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.mrn.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.mrn.value');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.fullname');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.age');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.sex');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.dob.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.patient.dob.value');
		$I->seeResponseJsonMatchesJsonPath('$.data.admittype.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.admittype.value.id');
		$I->seeResponseJsonMatchesJsonPath('$.data.admittype.value.name');
		$I->seeResponseJsonMatchesJsonPath('$.data.locationid');
		$I->seeResponseJsonMatchesJsonPath('$.data.locationname');
		$I->seeResponseJsonMatchesJsonPath('$.data.datestart');
		$I->seeResponseJsonMatchesJsonPath('$.data.datefinish');
		$I->seeResponseJsonMatchesJsonPath('$.data.procedureinfo.*.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.procedureinfo.*.value');
		$I->seeResponseJsonMatchesJsonPath('$.data.description.*.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.description.*.value');
		$I->seeResponseJsonMatchesJsonPath('$.data.custom_fields');
		$I->seeResponseJsonMatchesJsonPath('$.data.staff');
		$I->seeResponseJsonMatchesJsonPath('$.data.stafflist');
		$I->seeResponseJsonMatchesJsonPath('$.data.cards.staff');
		$I->seeResponseJsonMatchesJsonPath('$.data.cards.location');
		$I->seeResponseJsonMatchesJsonPath('$.data.staffinfo.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.staffinfo.staffs.*.show');
		$I->seeResponseJsonMatchesJsonPath('$.data.staffinfo.staffs.*.value');
	}

	protected function checkResponseJsonTypes(ApiTester $I)
	{
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.patient.mrn');
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.patient.dob');
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.admittype');
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.procedureinfo.*');
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.description.*');
		$I->seeResponseMatchesJsonType(['show' => 'boolean'], '$.data.staffinfo.staffs.*');
	}

	public function testCases(ApiTester $I)
	{

	}

	public function testGetCase(ApiTester $I)
	{
		$id = 1517;
		$I->sendGET('/?route=cases/case&caseid='.$id);
		$I->seeResponseContainsJson([
			'data' => [
				'caseid' => $id,
			]
		]);
		$this->checkJSONStructure($I);
		$this->checkResponseJsonTypes($I);
	}

	public function testGetMyCases(ApiTester $I)
	{
		$I->sendGET('/?route=cases/mycases');
		$I->seeResponseJsonMatchesJsonPath('$.data.cases.*.patient');
		$I->seeResponseJsonMatchesJsonPath('$.data.cases.*.caseid');
		$I->seeResponseJsonMatchesJsonPath('$.data.cases.*.admittype');
	}

	public function testSaveCase(ApiTester $I)
	{
		$id = 1517;
		$I->sendGET('/?route=cases/case&caseid='.$id);
		$res = $I->grabJsonResponse()->toArray();
		$I->sendPOST('/?route=save/report', ['data' => json_encode($res['data'])]);
		$I->seeResponseContainsJson([
			'data' => [
				'id' => $res['data']['reportid'],
			]
		]);
	}

	public function testGetIcds(ApiTester $I)
	{
		$I->sendGET('/?route=cases/icds&offset=0&limit=20');
		$I->seeResponseJsonMatchesJsonPath('$.data.icds.*.id');
		$I->seeResponseJsonMatchesJsonPath('$.data.icds.*.code');
		$I->seeResponseJsonMatchesJsonPath('$.data.icds.*.desc');
	}

	public function testGetSurgeryTypes(ApiTester $I)
	{
		$I->sendGET('/?route=cases/surgerytypes');
		$I->seeResponseJsonMatchesJsonPath('$.data.types.*.surgerytypeid');
		$I->seeResponseJsonMatchesJsonPath('$.data.types.*.surgerytypecode');
		$I->seeResponseJsonMatchesJsonPath('$.data.types.*.surgerytypename');
	}

}
