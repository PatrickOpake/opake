<?php

namespace Opake\Model\User;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\User\Credentials\Alert;

class Credentials extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'user_credentials';
	protected $_row = [
		'id' => null,
		'user_id' => null,
		'npi_number' => '',
		'npi_file_id' => null,
		'medical_licence_number' => '',
		'medical_licence_exp_date' => null,
		'medical_licence_file_id' => null,
		'dea_number' => '',
		'dea_exp_date' => null,
		'dea_file_id' => null,
		'cds_number' => '',
		'cds_exp_date' => null,
		'cds_file_id' => null,
		'ecfmg' => '',
		'insurance' => '',
		'insurance_exp_date' => null,
		'insurance_reappointment_date' => null,
		'insurance_file_id' => null,
		'acls_date' => null,
		'acls_file_id' => null,
		'immunizations_ppp_due' => null,
		'immunizations_help_b' => null,
		'immunizations_rubella' => null,
		'immunizations_rubeola' => null,
		'immunizations_varicela' => null,
		'immunizations_mumps' => null,
		'immunizations_flue' => null,
		'immunizations_file_id' => null,
		'retest_date' => null,
		'upin' => '',
		'licence_number' => '',
		'licence_expr_date' => null,
		'licence_file_id' => null,
		'bls_date' => null,
		'bls_file_id' => null,
		'cnor_date' => null,
		'cnor_file_id' => null,
		'malpractice' => '',
		'malpractice_exp_date' => null,
		'malpractice_file_id' => null,
		'hp_exp_date' => null,
		'hp_file_id' => null,
		'taxonomy_code' => null,
		'tin' => null,
	];

	protected $belongs_to = [
		'user' => [
			'model' => 'User',
			'key' => 'user_id',
		],
		'npi_file' => [
			'model' => 'UploadedFile',
			'key' => 'npi_file_id'
		],
		'medical_licence_file' => [
			'model' => 'UploadedFile',
			'key' => 'medical_licence_file_id'
		],
		'dea_file' => [
			'model' => 'UploadedFile',
			'key' => 'dea_file_id'
		],
		'cds_file' => [
			'model' => 'UploadedFile',
			'key' => 'cds_file_id'
		],
		'insurance_file' => [
			'model' => 'UploadedFile',
			'key' => 'insurance_file_id'
		],
		'acls_file' => [
			'model' => 'UploadedFile',
			'key' => 'acls_file_id'
		],
		'immunizations_file' => [
			'model' => 'UploadedFile',
			'key' => 'immunizations_file_id'
		],
		'licence_file' => [
			'model' => 'UploadedFile',
			'key' => 'licence_file_id'
		],
		'bls_file' => [
			'model' => 'UploadedFile',
			'key' => 'bls_file_id'
		],
		'cnor_file' => [
			'model' => 'UploadedFile',
			'key' => 'cnor_file_id'
		],
		'malpractice_file' => [
			'model' => 'UploadedFile',
			'key' => 'malpractice_file_id'
		],
		'hp_file' => [
			'model' => 'UploadedFile',
			'key' => 'hp_file_id'
		],
	];

	public function fromArray($data)
	{
		if (isset($data->medical_licence_exp_date) && $data->medical_licence_exp_date) {
			$data->medical_licence_exp_date = TimeFormat::formatToDB($data->medical_licence_exp_date);
		}
		if (isset($data->dea_exp_date) && $data->dea_exp_date) {
			$data->dea_exp_date = TimeFormat::formatToDB($data->dea_exp_date);
		}
		if (isset($data->cds_exp_date) && $data->cds_exp_date) {
			$data->cds_exp_date = TimeFormat::formatToDB($data->cds_exp_date);
		}
		if (isset($data->insurance_exp_date) && $data->insurance_exp_date) {
			$data->insurance_exp_date = TimeFormat::formatToDB($data->insurance_exp_date);
		}
		if (isset($data->insurance_reappointment_date) && $data->insurance_reappointment_date) {
			$data->insurance_reappointment_date = TimeFormat::formatToDB($data->insurance_reappointment_date);
		}
		if (isset($data->immunizations_ppp_due) && $data->immunizations_ppp_due) {
			$data->immunizations_ppp_due = TimeFormat::formatToDB($data->immunizations_ppp_due);
		}
		if (isset($data->immunizations_help_b) && $data->immunizations_help_b) {
			$data->immunizations_help_b = TimeFormat::formatToDB($data->immunizations_help_b);
		}
		if (isset($data->immunizations_rubella) && $data->immunizations_rubella) {
			$data->immunizations_rubella = TimeFormat::formatToDB($data->immunizations_rubella);
		}
		if (isset($data->immunizations_rubeola) && $data->immunizations_rubeola) {
			$data->immunizations_rubeola = TimeFormat::formatToDB($data->immunizations_rubeola);
		}
		if (isset($data->immunizations_varicela) && $data->immunizations_varicela) {
			$data->immunizations_varicela = TimeFormat::formatToDB($data->immunizations_varicela);
		}
		if (isset($data->immunizations_mumps) && $data->immunizations_mumps) {
			$data->immunizations_mumps = TimeFormat::formatToDB($data->immunizations_mumps);
		}
		if (isset($data->immunizations_flue) && $data->immunizations_flue) {
			$data->immunizations_flue = TimeFormat::formatToDB($data->immunizations_flue);
		}
		if (isset($data->retest_date) && $data->retest_date) {
			$data->retest_date = TimeFormat::formatToDB($data->retest_date);
		}
		if (isset($data->licence_expr_date) && $data->licence_expr_date) {
			$data->licence_expr_date = TimeFormat::formatToDB($data->licence_expr_date);
		}
		if (isset($data->bls_date) && $data->bls_date) {
			$data->bls_date = TimeFormat::formatToDB($data->bls_date);
		}
		if (isset($data->acls_date) && $data->acls_date) {
			$data->acls_date = TimeFormat::formatToDB($data->acls_date);
		}
		if (isset($data->cnor_date) && $data->cnor_date) {
			$data->cnor_date = TimeFormat::formatToDB($data->cnor_date);
		}
		if (isset($data->malpractice_exp_date) && $data->malpractice_exp_date) {
			$data->malpractice_exp_date = TimeFormat::formatToDB($data->malpractice_exp_date);
		}
		if (isset($data->hp_exp_date) && $data->hp_exp_date) {
			$data->hp_exp_date = TimeFormat::formatToDB($data->hp_exp_date);
		}

		if (isset($data->npi_file) && $data->npi_file) {
			$data->npi_file_id = $data->npi_file->id;
		}
		if (isset($data->medical_licence_file) && $data->medical_licence_file) {
			$data->medical_licence_file_id = $data->medical_licence_file->id;
		}
		if (isset($data->dea_file) && $data->dea_file) {
			$data->dea_file_id = $data->dea_file->id;
		}
		if (isset($data->cds_file) && $data->cds_file) {
			$data->cds_file_id = $data->cds_file->id;
		}
		if (isset($data->insurance_file) && $data->insurance_file) {
			$data->insurance_file_id = $data->insurance_file->id;
		}
		if (isset($data->acls_file) && $data->acls_file) {
			$data->acls_file_id = $data->acls_file->id;
		}
		if (isset($data->immunizations_file) && $data->immunizations_file) {
			$data->immunizations_file_id = $data->immunizations_file->id;
		}
		if (isset($data->licence_file) && $data->licence_file) {
			$data->licence_file_id = $data->licence_file->id;
		}
		if (isset($data->bls_file) && $data->bls_file) {
			$data->bls_file_id = $data->bls_file->id;
		}
		if (isset($data->cnor_file) && $data->cnor_file) {
			$data->cnor_file_id = $data->cnor_file->id;
		}
		if (isset($data->malpractice_file) && $data->malpractice_file) {
			$data->malpractice_file_id = $data->malpractice_file->id;
		}
		if (isset($data->hp_file) && $data->hp_file) {
			$data->hp_file_id = $data->hp_file->id;
		}

		return $data;
	}

	public function setAlertInactive()
	{
		$this->pixie->db->query('update')->table('user_credentials_alert')
			->data(['status' => Alert::STATUS_INACTIVE])
			->where('credentials_id', $this->id())
			->execute();
	}

	public function toArray()
	{
		$data = [
			'id' => (int) $this->id,
			'user_id' => (int) $this->user_id,
			'npi_number' => $this->npi_number,
			'npi_file_id' => (int) $this->npi_file_id,
			'npi_file' => $this->getFileArray($this->npi_file),
			'medical_licence_number' => $this->medical_licence_number,
			'medical_licence_exp_date' => $this->medical_licence_exp_date,
			'medical_licence_file_id' => (int) $this->medical_licence_file_id,
			'medical_licence_file' => $this->getFileArray($this->medical_licence_file),
			'dea_number' => $this->dea_number,
			'dea_exp_date' => $this->dea_exp_date,
			'dea_file_id' => (int) $this->dea_file_id,
			'dea_file' => $this->getFileArray($this->dea_file),
			'cds_number' => $this->cds_number,
			'cds_exp_date' => $this->cds_exp_date,
			'cds_file_id' => (int) $this->cds_file_id,
			'cds_file' => $this->getFileArray($this->cds_file),
			'ecfmg' => $this->ecfmg,
			'insurance' => $this->insurance,
			'insurance_exp_date' => $this->insurance_exp_date,
			'insurance_reappointment_date' => $this->insurance_reappointment_date,
			'insurance_file_id' => (int) $this->insurance_file_id,
			'insurance_file' => $this->getFileArray($this->insurance_file),
			'acls_date' => $this->acls_date,
			'acls_file_id' => (int) $this->acls_file_id,
			'acls_file' => $this->getFileArray($this->acls_file),
			'immunizations_ppp_due' => $this->immunizations_ppp_due,
			'immunizations_help_b' => $this->immunizations_help_b,
			'immunizations_rubella' => $this->immunizations_rubella,
			'immunizations_rubeola' => $this->immunizations_rubeola,
			'immunizations_varicela' => $this->immunizations_varicela,
			'immunizations_mumps' => $this->immunizations_mumps,
			'immunizations_flue' => $this->immunizations_flue,
			'immunizations_file_id' => (int) $this->immunizations_file_id,
			'immunizations_file' => $this->getFileArray($this->immunizations_file),
			'retest_date' => $this->retest_date,
			'upin' => $this->upin,
			'licence_number' => $this->licence_number,
			'licence_expr_date' => $this->licence_expr_date,
			'licence_file_id' => (int) $this->licence_file_id,
			'licence_file' => $this->getFileArray($this->licence_file),
			'bls_date' => $this->bls_date,
			'bls_file_id' => (int) $this->bls_file_id,
			'bls_file' => $this->getFileArray($this->bls_file),
			'cnor_date' => $this->cnor_date,
			'cnor_file_id' => (int) $this->cnor_file_id,
			'cnor_file' => $this->getFileArray($this->cnor_file),
			'malpractice' => $this->malpractice,
			'malpractice_exp_date' => $this->malpractice_exp_date,
			'malpractice_file_id' => (int) $this->malpractice_file_id,
			'malpractice_file' => $this->getFileArray($this->malpractice_file),
			'hp_exp_date' => $this->hp_exp_date,
			'hp_file_id' => (int) $this->hp_file_id,
			'hp_file' => $this->getFileArray($this->hp_file),
			'taxonomy_code' => $this->taxonomy_code,
			'tin' => $this->tin
		];

		return $data;
	}

	public function toShortArray()
	{
		$data = [
			'id' => (int) $this->id,
			'user_id' => (int) $this->user_id,
			'user' => [
				'first_name' => $this->user->first_name,
				'last_name' => $this->user->last_name
			],
			'npi_number' => $this->npi_number,
			'npi_file_url' => $this->npi_file_id ? $this->npi_file->getWebPath() : null,
			'medical_licence_number' => $this->medical_licence_number,
			'medical_licence_exp_date' => $this->medical_licence_exp_date,
			'medical_licence_file_url' => $this->medical_licence_file_id ? $this->medical_licence_file->getWebPath() : null,
			'dea_number' => $this->dea_number,
			'dea_exp_date' => $this->dea_exp_date,
			'dea_file_url' => $this->dea_file_id ? $this->dea_file->getWebPath() : null,
			'cds_number' => $this->cds_number,
			'cds_exp_date' => $this->cds_exp_date,
			'cds_file_url' => $this->cds_file_id ? $this->cds_file->getWebPath() : null,
			'ecfmg' => $this->ecfmg,
			'insurance' => $this->insurance,
			'insurance_exp_date' => $this->insurance_exp_date,
			'insurance_reappointment_date' => $this->insurance_reappointment_date,
			'insurance_file_url' => $this->insurance_file_id ? $this->insurance_file->getWebPath() : null,
			'acls_date' => $this->acls_date,
			'acls_file_url' => $this->acls_file_id ? $this->acls_file->getWebPath() : null,
			'immunizations_ppp_due' => $this->immunizations_ppp_due,
			'immunizations_help_b' => $this->immunizations_help_b,
			'immunizations_rubella' => $this->immunizations_rubella,
			'immunizations_rubeola' => $this->immunizations_rubeola,
			'immunizations_varicela' => $this->immunizations_varicela,
			'immunizations_mumps' => $this->immunizations_mumps,
			'immunizations_flue' => $this->immunizations_flue,
			'immunizations_file_url' => $this->immunizations_file_id ? $this->immunizations_file->getWebPath() : null,
			'retest_date' => $this->retest_date,
			'upin' => $this->upin,
			'licence_number' => $this->licence_number,
			'licence_expr_date' => $this->licence_expr_date,
			'licence_file_url' => $this->licence_file_id ? $this->licence_file->getWebPath() : null,
			'bls_date' => $this->bls_date,
			'bls_file_url' => $this->bls_file_id ? $this->bls_file->getWebPath() : null,
			'cnor_date' => $this->cnor_date,
			'cnor_file_url' => $this->cnor_file_id ? $this->cnor_file->getWebPath() : null,
			'malpractice' => $this->malpractice,
			'malpractice_exp_date' => $this->malpractice_exp_date,
			'malpractice_file_url' => $this->malpractice_file_id ? $this->malpractice_file->getWebPath() : null,
			'hp_exp_date' => $this->hp_exp_date,
			'hp_file_url' => $this->hp_file_id ? $this->hp_file->getWebPath() : null,
			'taxonomy_code' => $this->taxonomy_code,
			'tin' => $this->tin
		];

		return $data;
	}

	protected function getFileArray($file)
	{
		if ($file && $file->loaded()) {
			return [
				'id' => $file->id,
				'uploaded_date' => date('D M d Y H:i:s O', strtotime($file->uploaded_date)),
				'url' => $file->getWebPath(),
				'mime_type' => $file->mime_type,
				'file_name' => $file->original_filename
			];
		}

		return null;

	}

}