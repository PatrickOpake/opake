<?php

namespace Opake\Model\Forms;

use Opake\Model\AbstractModel;

class ChartGroup extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'forms_chart_group';

	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'name' => null
	];

	protected $has_many = [
		'documents' => [
			'model' => 'Forms_Document',
			'through' => 'forms_chart_group_document',
			'key' => 'chart_group_id',
			'foreign_key' => 'form_document_id'
		]
	];

	public function getDocuments()
	{
		if (!$this->loaded()) {
			return [];
		}

		$model = $this->pixie->orm->get('Forms_Document');
		$model->query->join('forms_chart_group_document', [$model->table . '.id', 'forms_chart_group_document.form_document_id'], 'inner');
		$model->query->where('forms_chart_group_document.chart_group_id', $this->id());
		$model->query->fields($model->table . '.*');


		return $model->order_by('forms_chart_group_document.order')
			->find_all()
			->as_array();
	}

	public function getDocumentIds()
	{
		$documentIds = [];
		foreach ($this->getDocuments() as $document) {
			$documentIds[] = (int) $document->id();
		}

		return $documentIds;
	}

	public function updateDocuments($documentIds)
	{
		if ($this->loaded()) {

			$table = $this->has_many['documents']['through'];
			$chartCol = $this->has_many['documents']['key'];
			$docCol = $this->has_many['documents']['foreign_key'];
			$orderCol = 'order';

			$this->pixie->db->begin_transaction();

			$this->pixie->db->query('delete')
				->table($table)
				->where($chartCol, $this->id())
				->execute();

			if ($documentIds) {
				try {
					foreach ($documentIds as $num => $id) {
						$this->pixie->db->query('insert')
							->table($table)
							->data([
								$chartCol => $this->id(),
								$docCol => $id,
								$orderCol => $num
							])
							->execute();
					}

					$this->pixie->db->commit();

				} catch (\Exception $e) {
					$this->pixie->db->rollback();
					throw $e;
				}
			}
		}
	}

	public function toArray()
	{
		$documents = [];
		$documentIds = [];

		foreach ($this->getDocuments() as $document) {
			$documentIds[] = (int) $document->id();
			$documents[] = [
				'id' => (int) $document->id(),
				'name' => $document->name
			];
		}

		return [
			'id' => (int) $this->id(),
			'organization_id' => (int) $this->organization_id,
			'name' => $this->name,
			'documents' => $documents,
			'document_ids' => $documentIds
		];
	}

}