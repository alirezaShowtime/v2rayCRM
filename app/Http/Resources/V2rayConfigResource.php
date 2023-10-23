<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class V2rayConfigResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        
        return [
            'id' => $this->id,
            'remark' => $this->remark,
            'size' => $this->size,
            'price' => $this->price,
            'owner' => $this->user->username,
            'enabled_at' => Carbon::parse($this->enabled_at)->timestamp,
            'expired_at' => Carbon::parse($this->expired_at)->timestamp,
            'created_at' => Carbon::parse($this->created_ath)->timestamp,
            'days' => $this->days,
            'config_data' => $this->config_data,
        ];
    }
}
