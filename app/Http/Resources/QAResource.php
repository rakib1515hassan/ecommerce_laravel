<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QAResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            $this->collection->map(function ($item) {
                return [
                    "id" => $item->id,
                    'question' => $item->question,
                    'answer' => $item->answer,
                    'status' => $item->status,
                    'awswered_by_admin' => $item->awswered_by_admin,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'customer'   => new CustomerResource($item->customer),
                    'answered_by' => $item->answered_by != null ? ($item->awswered_by_admin == 1 ? new AdminResource($item->answeredByAdmin) : new SellerResource($item->answeredBySeller)) : null,
                ];
            })
        ];
    }
}
