<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now(); 
        
        $suppliers = [
            [
                'supplier_name' => 'Supplier A (Hardware Co.)',
                'contactNO' => '09123456781',
                'address' => '101 Main St, Manila',
                'is_active' => true,
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'supplier_name' => 'Supplier B (Plumbing & Electrics)',
                'contactNO' => '09123456782',
                'address' => '202 Secondary Rd, Cebu',
                'is_active' => true,
                'created_at' => $now, 
                'updated_at' => $now  
            ],
            [
                'supplier_name' => 'Supplier C (Tools & Safety)',
                'contactNO' => '09123456783',
                'address' => '303 Tool Zone, Davao',
                'is_active' => true,
                'created_at' => $now, 
                'updated_at' => $now  
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}
