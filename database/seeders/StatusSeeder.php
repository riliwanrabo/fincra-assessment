<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            'processing', 'successful', 'failed'
        ];

        $statusCollection = collect($statuses)->map(fn ($status) => (['name' => $status, 'slug' => Str::slug($status)]))->toArray();

        Status::upsert([...$statusCollection], ['name']);
    }
}
