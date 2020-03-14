<?php

namespace OpakePatients\Model\Cases\Registration;

class Document extends \Opake\Model\Cases\Registration\Document
{
	public function toArray()
	{
		$surgeons = [];
		foreach ($this->case_registration->case->users->find_all() as $user) {
			$surgeons[] = $user->toArray();
		}

		$data = [
			'id' => $this->id(),
			'status' => (int)$this->status,
			'type' => $this->type->id(),
			'name' => $this->type->name,
			'url' => ($this->file && $this->file->loaded()) ? $this->file->getWebPath() : null,
			'uploaded_date' => date('D M d Y H:i:s O', strtotime($this->uploaded_date)),
			'mime_type' => $this->file->mime_type,
			'dos' => $this->case_registration->case->time_start,
			'procedure' => $this->case_registration->case->type->toArray(),
			'surgeons' => $surgeons
		];
		return $data;
	}
}