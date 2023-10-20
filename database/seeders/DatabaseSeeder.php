<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        try {
            DB::table('inbounds')->insert([
                ["type" => "vless", "name" => "VLESS GRPC REALITY"],
                ["type" => "vless", "name" => "VLESS TCP REALITY"],
                ["type" => "vmess", "name" => "VMess TCP"],
                ["type" => "vmess", "name" => "VMess Websocket"],
                ["type" => "trojan", "name" => "Trojan Websocket TLS"],
                ["type" => "shadowsocks", "name" => "Shadowsocks TCP"],
            ]);
        } catch (UniqueConstraintViolationException  $e) {

            if ($e->getCode() != 23000) {
                throw new Exception();
            }
        }
    }
}
