<?php

namespace Opake\Model;


trait NoteTrait
{

	public function getReminder()
	{
		return $this->reminder
			->where('note_type', $this->type_note)
			->where('note_id', $this->id())
			->find();
	}

}