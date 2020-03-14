<?php

namespace OpakePatients\Controller\Api\Appointments\Forms;

use Opake\Controller\Cases\Patients\Forms\Influenza as OpakeInfluenzaContoller;
use OpakePatients\Controller\AbstractAjax;

class Influenza extends AbstractAjax
{
	use OpakeInfluenzaContoller;
}