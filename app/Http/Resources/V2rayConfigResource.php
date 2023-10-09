<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class V2rayConfigResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'remark' => $this->remark,
            'size' => $this->size,
            'price' => $this->price,
            'enabled_at' => $this->enabled_at,
            'expired_at' => $this->expired_at,
            'config_data' => $this->config_data,
        ];
    }
}
