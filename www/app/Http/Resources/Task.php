<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
{

	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'task' => $this->task,
			'is_done' => $this->is_done,
			'is_deleted' => $this->is_deleted
		];
	}
}