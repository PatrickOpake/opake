DROP FUNCTION IF EXISTS ledger_patient_outstanding_balance;
CREATE FUNCTION ledger_patient_outstanding_balance(pPatientId INT) RETURNS DECIMAL(12,2)
				DETERMINISTIC
BEGIN

			DECLARE patientInsurancesCount INT DEFAULT 0;
			DECLARE result DECIMAL(12,2) DEFAULT 0.00;
			SELECT count(id) INTO patientInsurancesCount FROM `patient_insurance_types` WHERE `patient_id` = pPatientId LIMIT 1;

			B1: BEGIN

        DECLARE patientHasInsurancesWithResp INT DEFAULT 0;

        DECLARE charges DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalAmount DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalInsAmount DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalCoPayAmount DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalCoInsAmount DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalDeductAmount DECIMAL(12,2) DEFAULT 0.00;
        DECLARE totalOOPAmount DECIMAL(12,2) DEFAULT 0.00;

        DECLARE billId INT;
        DECLARE assignedInsuranceType INT DEFAULT 0;
        DECLARE isSelfPay INT DEFAULT 0;
        DECLARE billsQueryDone INT DEFAULT 0;
        DECLARE isForcePatientResp INT DEFAULT 0;
        DECLARE billsQueryCurs CURSOR FOR
          SELECT DISTINCT case_coding_bill.id, case_coding_bill.amount,
          IF((ISNULL(case_coding.insurance_order)),
            (SELECT `type` FROM case_registration_insurance_types WHERE (`registration_id` = case_registration.id AND `order` = 1 AND `deleted` = 0) LIMIT 1),
            (SELECT `type` FROM case_registration_insurance_types WHERE (`registration_id` = case_registration.id AND `order` = case_coding.insurance_order AND `deleted` = 0) LIMIT 1)) as assigned_insurance_type,
          billing_ledger_applying_options.is_force_patient_resp
          FROM case_coding_bill
          INNER JOIN case_coding ON case_coding_bill.coding_id = case_coding.id
          INNER JOIN `case` ON `case`.id = case_coding.case_id
          INNER JOIN case_registration ON case_registration.case_id = `case`.id
          LEFT JOIN billing_ledger_applying_options ON case_coding_bill.id = billing_ledger_applying_options.coding_bill_id
          WHERE case_registration.patient_id = pPatientId;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET billsQueryDone = 1;

        OPEN billsQueryCurs;

        B1L1: LOOP
          FETCH billsQueryCurs INTO billId, charges, assignedInsuranceType, isForcePatientResp;
          IF billsQueryDone THEN
            LEAVE B1L1;
          END IF;

          IF ISNULL(charges) THEN
            SET charges = 0.00;
          END IF;

          SET isSelfPay = 0;
          SET patientHasInsurancesWithResp = 0;
          SET totalAmount = 0.00;
          SET totalInsAmount = 0.00;
          SET totalCoPayAmount = 0.00;
          SET totalCoInsAmount = 0.00;
          SET totalDeductAmount = 0.00;
          SET totalOOPAmount = 0.00;

          IF NOT ISNULL(assignedInsuranceType) AND (assignedInsuranceType = 5 OR assignedInsuranceType = 9) THEN
            SET isSelfPay = 1;
          END IF;

          IF NOT ISNULL(isForcePatientResp) AND (isForcePatientResp = 1) THEN
            SET isSelfPay = 1;
          END IF;

          B2: BEGIN
            DECLARE tempAmount DECIMAL(12,2);
            DECLARE tempCoPay DECIMAL(12,2);
            DECLARE tempCoIns DECIMAL(12,2);
            DECLARE tempDeduct DECIMAL(12,2);
            DECLARE tempPaySource INT;
            DECLARE paymentsQueryDone INT DEFAULT 0;
            DECLARE paymentsQueryCurs CURSOR FOR SELECT billing_ledger_applied_payment.amount, billing_ledger_applied_payment.resp_co_pay_amount, billing_ledger_applied_payment.resp_co_ins_amount, billing_ledger_applied_payment.resp_deduct_amount, billing_ledger_payment_info.payment_source
            FROM billing_ledger_applied_payment
            INNER JOIN billing_ledger_payment_info ON billing_ledger_applied_payment.payment_info_id = billing_ledger_payment_info.id
            WHERE billing_ledger_applied_payment.coding_bill_id = billId
            GROUP BY billing_ledger_applied_payment.id;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET paymentsQueryDone = 1;

            OPEN paymentsQueryCurs;

            B2L1: LOOP

                FETCH paymentsQueryCurs INTO tempAmount, tempCoPay, tempCoIns, tempDeduct, tempPaySource;
                IF paymentsQueryDone THEN
                  LEAVE B2L1;
                END IF;
                IF ISNULL(tempAmount) THEN
                    SET tempAmount = 0.00;
                END IF;

                SET totalAmount = totalAmount + tempAmount;
                IF tempPaySource = 1 THEN -- Insurance payment --
                  IF tempCoPay > 0 OR tempCoIns > 0 OR tempDeduct > 0 THEN
                    SET patientHasInsurancesWithResp = 1;
                  END IF;
                  IF tempCoPay > 0 THEN
                    SET totalCoPayAmount = totalCoPayAmount + tempCoPay;
                  END IF;
                  IF tempCoIns > 0 THEN
                    SET totalCoInsAmount = totalCoInsAmount + tempCoIns;
                  END IF;
                  IF tempDeduct > 0 THEN
                    SET totalDeductAmount = totalDeductAmount + tempDeduct;
                  END IF;
                ELSEIF tempPaySource = 2 OR tempPaySource = 8 THEN -- Co Pay --
                  SET totalCoPayAmount = totalCoPayAmount - tempAmount;
                ELSEIF tempPaySource = 4 OR tempPaySource = 9 THEN -- Co Ins --
                  SET totalCoInsAmount = totalCoInsAmount - tempAmount;
                ELSEIF tempPaySource = 3 OR tempPaySource = 10 THEN -- Deduct --
                  SET totalDeductAmount = totalDeductAmount - tempAmount;
                ELSE
                  BEGIN END;
                END IF;

            END LOOP B2L1;
            CLOSE paymentsQueryCurs;
          END B2;

          SET totalAmount = (charges - totalAmount);

         IF patientInsurancesCount > 0 AND patientHasInsurancesWithResp = 0 THEN
           SET totalInsAmount = totalAmount;
         END IF;

         IF isSelfPay = 0 THEN
          SET totalOOPAmount = (totalAmount - (totalInsAmount + totalCoPayAmount + totalCoInsAmount + totalDeductAmount));
         ELSE
          SET totalOOPAmount = totalAmount;
         END IF;

        SET result = result + (totalCoPayAmount + totalCoInsAmount + totalDeductAmount + totalOOPAmount);

        END LOOP B1L1;
        CLOSE billsQueryCurs;
		END B1;

  -- Return outstanding balance --
	RETURN result;

END