<?php

use \Console\Migration\BaseMigration;

class InsuranceTypesMigrateExistedData extends BaseMigration
{
    public function change()
    {
        $this->getDb()->begin_transaction();
        try {

            $this->migratePatientInsurances();
            $this->migrateRegistrationInsurances();
            $this->migrateBookingInsurances();

            $this->getDb()->commit();

        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }
    }

    protected function migratePatientInsurances()
    {

        $rows = $this->getDb()->query('select')
            ->table('patient_insurance')
            ->execute();

        $insurances = [];
        foreach ($rows as $row) {
            $objectId = $row->patient_id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            if ($row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT && $row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP) {
                if (!$row->type) {
                    $row->type = \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_COMMERCIAL;
                }
                $ins = [
                    'type' => $row->type,
                    'order' => $order,
                    'patient_id' => $objectId,
                    'row' => $row
                ];

                $insurances[$objectId][] = $ins;
            }

        }

        foreach ($insurances as $objectInsurances) {
            foreach ($objectInsurances as $insurance) {
                $row = $insurance['row'];

                $this->getDb()->query('insert')->table('insurance_data_regular')
                    ->data([
                        'insurance_id' => $row->insurance_id,
                        'last_name' => $row->last_name,
                        'first_name' => $row->first_name,
                        'middle_name' => $row->middle_name,
                        'suffix' => $row->suffix,
                        'dob' => $row->dob,
                        'gender' => $row->gender,
                        'phone' => $row->phone,
                        'address' => $row->address,
                        'apt_number' => $row->apt_number,
                        'country_id' => $row->country_id,
                        'state_id' => $row->state_id,
                        'custom_state' => $row->custom_state,
                        'city_id' => $row->city_id,
                        'custom_city' => $row->custom_city,
                        'zip_code' => $row->zip_code,
                        'relationship_to_insured' => $row->relationship_to_insured,
                        'type' => $row->type,
                        'policy_number' => $row->policy_number,
                        'group_number' => $row->group_number,
                        'provider_phone' => $row->provider_phone,
                        'insurance_verified' => $row->insurance_verified,
                        'is_pre_authorization_completed' => $row->is_pre_authorization_completed,
                        'address_insurance' => $row->address_insurance,
                    ])
                    ->execute();

                $dataId = $this->getDb()->insert_id();

                $this->getDb()->query('insert')->table('patient_insurance_types')
                    ->data([
                        'patient_id' => $insurance['patient_id'],
                        'type' => $insurance['type'],
                        'order' => $insurance['order'],
                        'insurance_data_id' => $dataId
                    ])
                    ->execute();
            }
        }
    }

    protected function migrateRegistrationInsurances()
    {
        $rows = $this->getDb()->query('select')
            ->table('case_registration_insurance')
            ->execute();

        $insurances = [];
        foreach ($rows as $row) {
            $objectId = $row->registration_id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            if ($row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT && $row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP) {
                if (!$row->type) {
                    $row->type = \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_COMMERCIAL;
                }
                $ins = [
                    'type' => $row->type,
                    'order' => $order,
                    'registration_id' => $objectId,
                    'row' => $row
                ];

                $insurances[$objectId][] = $ins;
            }

        }


        $rows = $this->getDb()->query('select')
            ->table('case_registration')
            ->where('patients_relations', 2)
            ->execute();

        foreach ($rows as $row) {
            $objectId = $row->id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            $ins = [
                'type' => \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT,
                'order' => $order,
                'registration_id' => $objectId,
                'row' => $row
            ];

            $insurances[$objectId][] = $ins;
        }

        $rows = $this->getDb()->query('select')
            ->table('case_registration')
            ->where('patients_relations', 3)
            ->execute();

        foreach ($rows as $row) {
            $objectId = $row->id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }


            $ins = [
                'type' => \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP,
                'order' => $order,
                'registration_id' => $objectId,
                'row' => $row
            ];

            $insurances[$objectId][] = $ins;
        }

        foreach ($insurances as $objectInsurances) {
            foreach ($objectInsurances as $insurance) {
                $row = $insurance['row'];
                if ($insurance['type'] == \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP) {

                    $this->getDb()->query('insert')->table('insurance_data_workers_comp')
                        ->data([
                            'insurance_name' => $row->work_comp_insurance_name,
                            'adjuster_name' => $row->work_comp_adjusters_name,
                            'claim' => $row->work_comp_claim,
                            'adjuster_phone' => $row->work_comp_adjuster_phone,
                            'insurance_address' => $row->work_comp_insurance_address,
                            'city_id' => $row->work_comp_city_id,
                            'state_id' => $row->work_comp_state_id,
                            'zip' => $row->work_comp_zip,
                            'accident_date' => $row->work_comp_accident_date,
                        ])
                        ->execute();

                    $dataId = $this->getDb()->insert_id();

                } else if ($insurance['type'] == \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT) {

                    $this->getDb()->query('insert')->table('insurance_data_auto_accident')
                        ->data([
                            'insurance_name' => $row->auto_insurance_name,
                            'adjuster_name' => $row->auto_adjust_name,
                            'claim' => $row->auto_claim,
                            'adjuster_phone' => $row->auto_adjuster_phone,
                            'insurance_address' => $row->auto_insurance_address,
                            'city_id' => $row->auto_city_id,
                            'state_id' => $row->auto_state_id,
                            'zip' => $row->auto_zip,
                            'accident_date' => $row->accident_date,
                            'attorney_name' => $row->attorney_name,
                            'attorney_phone' => $row->attorney_phone,
                        ])
                        ->execute();


                    $dataId = $this->getDb()->insert_id();

                } else {

                    $this->getDb()->query('insert')->table('insurance_data_regular')
                        ->data([
                            'insurance_id' => $row->insurance_id,
                            'last_name' => $row->last_name,
                            'first_name' => $row->first_name,
                            'middle_name' => $row->middle_name,
                            'suffix' => $row->suffix,
                            'dob' => $row->dob,
                            'gender' => $row->gender,
                            'phone' => $row->phone,
                            'address' => $row->address,
                            'apt_number' => $row->apt_number,
                            'country_id' => $row->country_id,
                            'state_id' => $row->state_id,
                            'custom_state' => $row->custom_state,
                            'city_id' => $row->city_id,
                            'custom_city' => $row->custom_city,
                            'zip_code' => $row->zip_code,
                            'relationship_to_insured' => $row->relationship_to_insured,
                            'type' => $row->type,
                            'policy_number' => $row->policy_number,
                            'group_number' => $row->group_number,
                            'provider_phone' => $row->provider_phone,
                            'insurance_verified' => $row->insurance_verified,
                            'is_pre_authorization_completed' => $row->is_pre_authorization_completed,
                            'address_insurance' => $row->address_insurance,
                        ])
                        ->execute();

                    $dataId = $this->getDb()->insert_id();
                }

                $this->getDb()->query('insert')->table('case_registration_insurance_types')
                    ->data([
                        'registration_id' => $insurance['registration_id'],
                        'type' => $insurance['type'],
                        'order' => $insurance['order'],
                        'insurance_data_id' => $dataId
                    ])
                    ->execute();
            }
        }

    }

    protected function migrateBookingInsurances()
    {
        $rows = $this->getDb()->query('select')
            ->table('booking_insurance')
            ->execute();

        $insurances = [];
        foreach ($rows as $row) {
            $objectId = $row->booking_id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            if ($row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT && $row->type != \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP) {
                if (!$row->type) {
                    $row->type = \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_COMMERCIAL;
                }
                $ins = [
                    'type' => $row->type,
                    'order' => $order,
                    'booking_id' => $objectId,
                    'row' => $row
                ];

                $insurances[$objectId][] = $ins;
            }

        }


        $rows = $this->getDb()->query('select')
            ->table('booking_sheet')
            ->where('patients_relations', 2)
            ->execute();

        foreach ($rows as $row) {
            $objectId = $row->id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            $ins = [
                'type' => \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT,
                'order' => $order,
                'booking_id' => $objectId,
                'row' => $row
            ];

            $insurances[$objectId][] = $ins;
        }

        $rows = $this->getDb()->query('select')
            ->table('booking_sheet')
            ->where('patients_relations', 3)
            ->execute();

        foreach ($rows as $row) {
            $objectId = $row->id;
            if (!isset($insurances[$objectId])) {
                $insurances[$objectId] = [];
            }

            if (count($insurances[$objectId]) > 2) {
                $order = 4;
            } else {
                $order = count($insurances[$objectId]) + 1;
            }

            $ins = [
                'type' => \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP,
                'order' => $order,
                'booking_id' => $objectId,
                'row' => $row
            ];

            $insurances[$objectId][] = $ins;
        }

        foreach ($insurances as $objectInsurances) {
            foreach ($objectInsurances as $insurance) {
                $row = $insurance['row'];
                if ($insurance['type'] == \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_WORKERS_COMP) {

                    $this->getDb()->query('insert')->table('insurance_data_workers_comp')
                        ->data([
                            'insurance_name' => $row->work_comp_insurance_name,
                            'adjuster_name' => $row->work_comp_adjusters_name,
                            'claim' => $row->work_comp_claim,
                            'adjuster_phone' => $row->work_comp_adjuster_phone,
                            'insurance_address' => $row->work_comp_insurance_address,
                            'city_id' => $row->work_comp_city_id,
                            'state_id' => $row->work_comp_state_id,
                            'zip' => $row->work_comp_zip,
                            'accident_date' => $row->work_comp_accident_date,
                        ])
                        ->execute();

                    $dataId = $this->getDb()->insert_id();

                } else if ($insurance['type'] == \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT) {

                    $this->getDb()->query('insert')->table('insurance_data_auto_accident')
                        ->data([
                            'insurance_name' => $row->auto_insurance_name,
                            'adjuster_name' => $row->auto_adjust_name,
                            'claim' => $row->auto_claim,
                            'adjuster_phone' => $row->auto_adjuster_phone,
                            'insurance_address' => $row->auto_insurance_address,
                            'city_id' => $row->auto_city_id,
                            'state_id' => $row->auto_state_id,
                            'zip' => $row->auto_zip,
                            'accident_date' => $row->accident_date,
                            'attorney_name' => $row->attorney_name,
                            'attorney_phone' => $row->attorney_phone,
                        ])
                        ->execute();


                    $dataId = $this->getDb()->insert_id();

                } else {

                    $this->getDb()->query('insert')->table('insurance_data_regular')
                        ->data([
                            'insurance_id' => $row->insurance_id,
                            'last_name' => $row->last_name,
                            'first_name' => $row->first_name,
                            'middle_name' => $row->middle_name,
                            'suffix' => $row->suffix,
                            'dob' => $row->dob,
                            'gender' => $row->gender,
                            'phone' => $row->phone,
                            'address' => $row->address,
                            'apt_number' => $row->apt_number,
                            'country_id' => $row->country_id,
                            'state_id' => $row->state_id,
                            'custom_state' => $row->custom_state,
                            'city_id' => $row->city_id,
                            'custom_city' => $row->custom_city,
                            'zip_code' => $row->zip_code,
                            'relationship_to_insured' => $row->relationship_to_insured,
                            'type' => $row->type,
                            'policy_number' => $row->policy_number,
                            'group_number' => $row->group_number,
                            'provider_phone' => $row->provider_phone,
                            'insurance_verified' => $row->insurance_verified,
                            'is_pre_authorization_completed' => $row->is_pre_authorization_completed,
                            'address_insurance' => $row->address_insurance,
                        ])
                        ->execute();

                    $dataId = $this->getDb()->insert_id();
                }

                $this->getDb()->query('insert')->table('booking_insurance_types')
                    ->data([
                        'booking_id' => $insurance['booking_id'],
                        'type' => $insurance['type'],
                        'order' => $insurance['order'],
                        'insurance_data_id' => $dataId
                    ])
                    ->execute();
            }
        }
    }
}
