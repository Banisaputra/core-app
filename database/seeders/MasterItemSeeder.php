<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MasterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $userId = 1;

        $categories = [
            [
                'code' => 'MK',
                'name' => 'Makanan',
                'ppn_percent' => 0,
                'margin_percent' => 10,
                'margin_price' => 0,
                'parent_id' => 0,
                'is_parent' => 1,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'MN',
                'name' => 'Minuman',
                'ppn_percent' => 0,
                'margin_percent' => 15,
                'margin_price' => 0,
                'parent_id' => 0,
                'is_parent' => 1,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'SN',
                'name' => 'Snack',
                'ppn_percent' => 0,
                'margin_percent' => 20,
                'margin_price' => 0,
                'parent_id' => 0,
                'is_parent' => 1,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'PC',
                'name' => 'Perlengkapan Cafe',
                'ppn_percent' => 11,
                'margin_percent' => 25,
                'margin_price' => 0,
                'parent_id' => 0,
                'is_parent' => 1,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Subkategori Makanan
            [
                'code' => 'MK-MKN',
                'name' => 'Makanan Berat',
                'ppn_percent' => 0,
                'margin_percent' => 12,
                'margin_price' => 0,
                'parent_id' => 1001,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'MK-RNG',
                'name' => 'Makanan Ringan',
                'ppn_percent' => 0,
                'margin_percent' => 8,
                'margin_price' => 0,
                'parent_id' => 1001,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'MK-PST',
                'name' => 'Pastry',
                'ppn_percent' => 0,
                'margin_percent' => 15,
                'margin_price' => 0,
                'parent_id' => 1001,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'MK-DSR',
                'name' => 'Dessert',
                'ppn_percent' => 0,
                'margin_percent' => 18,
                'margin_price' => 0,
                'parent_id' => 1001,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Subkategori Minuman (tambahkan sesuai kebutuhan)
            [
                'code' => 'MN-PNS',
                'name' => 'Minuman Panas',
                'ppn_percent' => 0,
                'margin_percent' => 12,
                'margin_price' => 0,
                'parent_id' => 1002,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'MN-DNG',
                'name' => 'Minuman Dingin',
                'ppn_percent' => 0,
                'margin_percent' => 15,
                'margin_price' => 0,
                'parent_id' => 1002,
                'is_parent' => 0,
                'is_active' => 1,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Format: [item_code, item_name, ct_id, stock, hpp, sales_price, item_image, is_transactional]
        $products = [
            
            // Makanan (ct_id: 1001 dan subkategorinya)
            ['IND-001', 'Indomie Goreng', 2002, 65, 2100, 2310, 'indomie.jpg', 1],
            ['IND-002', 'Indomie Soto', 2002, 45, 2200, 2420, 'indomie_soto.jpg', 1],
            ['SED-001', 'Sedap Kari', 2002, 40, 2300, 2530, 'sedap.jpg', 1],
            ['SED-002', 'Sedap Ayam Bawang', 2002, 35, 2400, 2640, 'sedap_ayam.jpg', 1],
            ['CHI-001', 'Chitato Original', 2002, 60, 8000, 8800, 'chitato.jpg', 1],
            ['LAY-001', 'Lays Rumput Laut', 2002, 55, 8500, 9350, 'lays.jpg', 1],
            ['PIL-001', 'Pilus Garuda', 2002, 70, 3500, 3850, 'pilus.jpg', 1],
            ['CHE-001', 'Cheetos Balado', 2002, 65, 7000, 7700, 'cheetos.jpg', 1],
            ['TAN-001', 'Tango Wafer', 2002, 50, 6000, 6600, 'tango.jpg', 1],
            ['BEN-001', 'Beng-Beng', 2002, 80, 3500, 3850, 'bengbeng.jpg', 1],
            
            // Makanan Berat (ct_id: 2001)
            ['NAS-001', 'Nasi Putih', 2001, 100, 3000, 3300, 'nasi.jpg', 1],
            ['AYA-001', 'Ayam Goreng', 2001, 50, 12000, 13200, 'ayam_goreng.jpg', 1],
            ['IKA-001', 'Ikan Goreng', 2001, 40, 15000, 16500, 'ikan_goreng.jpg', 1],
            ['TEL-003', 'Telur Dadar', 2001, 60, 5000, 5500, 'telur_dadar.jpg', 1],
            ['TEM-001', 'Tempe Goreng', 2001, 70, 3000, 3300, 'tempe.jpg', 1],
            ['TAU-001', 'Tahu Goreng', 2001, 65, 2500, 2750, 'tahu.jpg', 1],
            ['SOP-001', 'Sop Ayam', 2001, 45, 8000, 8800, 'sop_ayam.jpg', 1],
            ['SOT-001', 'Soto Ayam', 2001, 35, 10000, 11000, 'soto.jpg', 1],
            ['REN-001', 'Rendang Sapi', 2001, 25, 18000, 19800, 'rendang.jpg', 1],
            ['GUL-004', 'Gulai Kambing', 2001, 20, 20000, 22000, 'gulai.jpg', 1],
            
            // Pastry (ct_id: 2003)
            ['ROT-001', 'Roti Tawar', 2003, 40, 10000, 11500, 'roti_tawar.jpg', 1],
            ['ROT-002', 'Roti Coklat', 2003, 35, 12000, 13800, 'roti_coklat.jpg', 1],
            ['ROT-003', 'Roti Keju', 2003, 30, 15000, 17250, 'roti_keju.jpg', 1],
            ['KUE-001', 'Brownies', 2003, 25, 8000, 9200, 'brownies.jpg', 1],
            ['KUE-002', 'Donat', 2003, 50, 5000, 5750, 'donat.jpg', 1],
            ['KUE-003', 'Croissant', 2003, 20, 10000, 11500, 'croissant.jpg', 1],
            ['KUE-004', 'Puff Pastry', 2003, 15, 12000, 13800, 'puff_pastry.jpg', 1],
            ['KUE-005', 'Tart Buah', 2003, 10, 25000, 28750, 'tart.jpg', 1],
            ['KUE-006', 'Muffin Coklat', 2003, 30, 7000, 8050, 'muffin.jpg', 1],
            ['KUE-007', 'Cupcake', 2003, 40, 6000, 6900, 'cupcake.jpg', 1],
            
            // Dessert (ct_id: 2004)
            ['ICE-001', 'Ice Cream Vanilla', 2004, 50, 8000, 9440, 'ice_cream.jpg', 1],
            ['ICE-002', 'Ice Cream Coklat', 2004, 45, 8500, 10030, 'ice_cream_coklat.jpg', 1],
            ['ICE-003', 'Ice Cream Stroberi', 2004, 40, 9000, 10620, 'ice_cream_stroberi.jpg', 1],
            ['PUD-001', 'Puding Coklat', 2004, 60, 5000, 5900, 'puding.jpg', 1],
            ['PUD-002', 'Puding Vanilla', 2004, 55, 5500, 6490, 'puding_vanilla.jpg', 1],
            ['GEL-001', 'Gelato', 2004, 30, 12000, 14160, 'gelato.jpg', 1],
            ['YOG-001', 'Yogurt', 2004, 35, 7000, 8260, 'yogurt.jpg', 1],
            ['PAI-001', 'Pie Apple', 2004, 25, 10000, 11800, 'pie_apple.jpg', 1],
            ['PAI-002', 'Pie Blueberry', 2004, 20, 11000, 12980, 'pie_blueberry.jpg', 1],
            ['FLN-001', 'Flan Caramel', 2004, 15, 8000, 9440, 'flan.jpg', 1],
            
            // Minuman Panas (ct_id: 3001)
            ['KOP-004', 'Kopi Hitam', 3001, 80, 3000, 3360, 'kopi_hitam.jpg', 1],
            ['KOP-005', 'Kopi Susu', 3001, 75, 4000, 4480, 'kopi_susu.jpg', 1],
            ['TEH-004', 'Teh Tarik', 3001, 70, 5000, 5600, 'teh_tarik.jpg', 1],
            ['TEH-005', 'Teh Manis', 3001, 85, 2000, 2240, 'teh_manis.jpg', 1],
            ['CAP-001', 'Cappuccino', 3001, 60, 7000, 7840, 'cappuccino.jpg', 1],
            ['LAT-001', 'Latte', 3001, 55, 8000, 8960, 'latte.jpg', 1],
            ['MAC-001', 'Macchiato', 3001, 40, 9000, 10080, 'macchiato.jpg', 1],
            ['MOC-001', 'Mocha', 3001, 35, 10000, 11200, 'mocha.jpg', 1],
            ['AME-001', 'Americano', 3001, 50, 6000, 6720, 'americano.jpg', 1],
            ['ESP-001', 'Espresso', 3001, 45, 5000, 5600, 'espresso.jpg', 1],
            
            // Minuman Dingin (ct_id: 3002)
            ['AQU-001', 'Aqua 600ml', 3002, 100, 2000, 2300, 'aqua.jpg', 1],
            ['LEM-001', 'Le Minerale 600ml', 3002, 90, 2100, 2415, 'leminerale.jpg', 1],
            ['VIT-001', 'Vit 600ml', 3002, 80, 2200, 2530, 'vit.jpg', 1],
            ['COC-001', 'Coca Cola 350ml', 3002, 85, 4000, 4600, 'cocacola.jpg', 1],
            ['SPR-001', 'Sprite 350ml', 3002, 75, 4000, 4600, 'sprite.jpg', 1],
            ['FAN-001', 'Fanta 350ml', 3002, 65, 4000, 4600, 'fanta.jpg', 1],
            ['POC-001', 'Pocari Sweat', 3002, 60, 7000, 8050, 'pocari.jpg', 1],
            ['MIL-001', 'Mizone', 3002, 55, 6000, 6900, 'mizone.jpg', 1],
            ['TEH-006', 'Teh Botol', 3002, 70, 3500, 4025, 'tehbotol.jpg', 1],
            ['TEH-007', 'Teh Pucuk', 3002, 65, 3500, 4025, 'tehpucuk.jpg', 1],
            
            // Jus Buah (ct_id: 3003)
            ['JUS-004', 'Jus Jeruk', 3003, 50, 8000, 9600, 'jus_jeruk.jpg', 1],
            ['JUS-005', 'Jus Alpukat', 3003, 45, 10000, 12000, 'jus_alpukat.jpg', 1],
            ['JUS-006', 'Jus Mangga', 3003, 40, 9000, 10800, 'jus_mangga.jpg', 1],
            ['JUS-007', 'Jus Melon', 3003, 35, 8500, 10200, 'jus_melon.jpg', 1],
            ['JUS-008', 'Jus Semangka', 3003, 60, 7000, 8400, 'jus_semangka.jpg', 1],
            ['JUS-009', 'Jus Wortel', 3003, 30, 6000, 7200, 'jus_wortel.jpg', 1],
            ['JUS-010', 'Jus Tomat', 3003, 25, 5000, 6000, 'jus_tomat.jpg', 1],
            ['JUS-011', 'Jus Nanas', 3003, 55, 7500, 9000, 'jus_nanas.jpg', 1],
            ['JUS-012', 'Jus Stroberi', 3003, 20, 12000, 14400, 'jus_stroberi.jpg', 1],
            ['JUS-013', 'Jus Apel', 3003, 40, 9500, 11400, 'jus_apel.jpg', 1],
            
            // Kopi Spesial (ct_id: 3004)
            ['KOP-006', 'Kopi Luwak', 3004, 15, 25000, 31250, 'kopi_luwak.jpg', 1],
            ['KOP-007', 'Kopi Toraja', 3004, 20, 20000, 25000, 'kopi_toraja.jpg', 1],
            ['KOP-008', 'Kopi Aceh', 3004, 18, 22000, 27500, 'kopi_aceh.jpg', 1],
            ['KOP-009', 'Kopi Bali', 3004, 22, 18000, 22500, 'kopi_bali.jpg', 1],
            ['KOP-010', 'Kopi Flores', 3004, 16, 23000, 28750, 'kopi_flores.jpg', 1],
            ['KOP-011', 'Kopi Java', 3004, 25, 15000, 18750, 'kopi_java.jpg', 1],
            ['KOP-012', 'Kopi Mandailing', 3004, 19, 21000, 26250, 'kopi_mandailing.jpg', 1],
            ['KOP-013', 'Kopi Sidikalang', 3004, 17, 24000, 30000, 'kopi_sidikalang.jpg', 1],
            ['KOP-014', 'Kopi Wamena', 3004, 14, 26000, 32500, 'kopi_wamena.jpg', 1],
            ['KOP-015', 'Kopi Kintamani', 3004, 21, 19000, 23750, 'kopi_kintamani.jpg', 1],
            
            // Teh (ct_id: 3005)
            ['TEH-008', 'Teh Hijau', 3005, 40, 6000, 6900, 'teh_hijau.jpg', 1],
            ['TEH-009', 'Teh Oolong', 3005, 35, 8000, 9200, 'teh_oolong.jpg', 1],
            ['TEH-010', 'Teh Hitam', 3005, 50, 5000, 5750, 'teh_hitam.jpg', 1],
            ['TEH-011', 'Teh Chamomile', 3005, 30, 10000, 11500, 'teh_chamomile.jpg', 1],
            ['TEH-012', 'Teh Peppermint', 3005, 25, 9000, 10350, 'teh_peppermint.jpg', 1],
            ['TEH-013', 'Teh Melati', 3005, 45, 7000, 8050, 'teh_melati.jpg', 1],
            ['TEH-014', 'Teh Jahe', 3005, 40, 6000, 6900, 'teh_jahe.jpg', 1],
            ['TEH-015', 'Teh Lemon', 3005, 55, 5500, 6325, 'teh_lemon.jpg', 1],
            ['TEH-016', 'Teh Rosella', 3005, 20, 12000, 13800, 'teh_rosella.jpg', 1],
            ['TEH-017', 'Teh Serai', 3005, 35, 7500, 8625, 'teh_serai.jpg', 1],
            
            // Snack - Keripik & Kerupuk (ct_id: 4001)
            ['KER-004', 'Keripik Kentang', 4001, 50, 7000, 8050, 'keripik_kentang.jpg', 1],
            ['KER-005', 'Keripik Singkong', 4001, 60, 6000, 6900, 'keripik_singkong.jpg', 1],
            ['KER-006', 'Keripik Pisang', 4001, 45, 8000, 9200, 'keripik_pisang.jpg', 1],
            ['KER-007', 'Kerupuk Udang Besar', 4001, 70, 4000, 4600, 'kerupuk_udang_besar.jpg', 1],
            ['KER-008', 'Kerupuk Bawang Putih', 4001, 65, 3500, 4025, 'kerupuk_bawang_putih.jpg', 1],
            ['KER-009', 'Kerupuk Ikan Tenggiri', 4001, 40, 5000, 5750, 'kerupuk_ikan.jpg', 1],
            ['KER-010', 'Kerupuk Kulit', 4001, 55, 4500, 5175, 'kerupuk_kulit.jpg', 1],
            ['KER-011', 'Kerupuk Seblak', 4001, 30, 6000, 6900, 'kerupuk_seblak.jpg', 1],
            ['KER-012', 'Kerupuk Melarat', 4001, 75, 3000, 3450, 'kerupuk_melarat.jpg', 1],
            ['KER-013', 'Kerupuk Amplang', 4001, 25, 9000, 10350, 'kerupuk_amplang.jpg', 1],
            
            // Snack - Kue Kering (ct_id: 4002)
            ['KUE-008', 'Kastangel', 4002, 40, 12000, 14400, 'kastangel.jpg', 1],
            ['KUE-009', 'Nastar', 4002, 35, 15000, 18000, 'nastar.jpg', 1],
            ['KUE-010', 'Putri Salju', 4002, 30, 13000, 15600, 'putri_salju.jpg', 1],
            ['KUE-011', 'Semprit', 4002, 45, 10000, 12000, 'semprit.jpg', 1],
            ['KUE-012', 'Kue Lidah Kucing', 4002, 50, 8000, 9600, 'lidah_kucing.jpg', 1],
            ['KUE-013', 'Kacang Telur', 4002, 60, 7000, 8400, 'kacang_telur.jpg', 1],
            ['KUE-014', 'Kue Keju', 4002, 25, 18000, 21600, 'kue_keju.jpg', 1],
            ['KUE-015', 'Choco Chip', 4002, 40, 14000, 16800, 'choco_chip.jpg', 1],
            ['KUE-016', 'Almond Cookies', 4002, 20, 20000, 24000, 'almond_cookies.jpg', 1],
            ['KUE-017', 'Kue Lumpur', 4002, 55, 6000, 7200, 'kue_lumpur.jpg', 1],
            
            // Snack - Cokelat & Permen (ct_id: 4003)
            ['CHO-001', 'Cokelat Silverqueen', 4003, 40, 12000, 14400, 'silverqueen.jpg', 1],
            ['CHO-002', 'Cokelat Cadbury', 4003, 35, 15000, 18000, 'cadbury.jpg', 1],
            ['CHO-003', 'Cokelat Toblerone', 4003, 30, 18000, 21600, 'toblerone.jpg', 1],
            ['CHO-004', 'Cokelat KitKat', 4003, 50, 10000, 12000, 'kitkat.jpg', 1],
            ['CHO-005', 'Cokelat Hersheys', 4003, 25, 16000, 19200, 'hersheys.jpg', 1],
            ['PER-006', 'Permen Kopiko 78', 4003, 100, 400, 480, 'kopiko78_permen.jpg', 1],
            ['PER-007', 'Permen Mintz', 4003, 120, 350, 420, 'mintz.jpg', 1],
            ['PER-008', 'Permen YoyiC', 4003, 90, 300, 360, 'yoyic.jpg', 1],
            ['PER-009', 'Permen Foxs', 4003, 80, 400, 480, 'foxs.jpg', 1],
            ['PER-010', 'Permen Nano-Nano', 4003, 70, 350, 420, 'nano_nano.jpg', 1],
            
            // Perlengkapan Cafe - Gelas & Mug (ct_id: 5001)
            ['GEL-002', 'Gelas Beling', 5001, 30, 8000, 9600, 'gelas_beling.jpg', 1],
            ['GEL-003', 'Gelas Kaca', 5001, 25, 10000, 12100, 'gelas_kaca.jpg', 1],
            ['MUG-001', 'Mug Keramik', 5001, 20, 15000, 18150, 'mug_keramik.jpg', 1],
            ['MUG-002', 'Mug Porcelain', 5001, 18, 18000, 21780, 'mug_porcelain.jpg', 1],
            ['MUG-003', 'Mug Stainless', 5001, 15, 20000, 24200, 'mug_stainless.jpg', 1],
            ['PIR-001', 'Piring Saji', 5001, 35, 12000, 14520, 'piring_saji.jpg', 1],
            ['PIR-002', 'Piring Makan', 5001, 40, 10000, 12100, 'piring_makan.jpg', 1],
            ['MNG-001', 'Mangkok Soup', 5001, 30, 8000, 9680, 'mangkok_soup.jpg', 1],
            ['MNG-002', 'Mangkok Nasi', 5001, 45, 7000, 8470, 'mangkok_nasi.jpg', 1],
            ['SND-001', 'Sendok Garpu Set', 5001, 25, 25000, 30250, 'sendok_garpu.jpg', 1],
        ];
        DB::beginTransaction();
        try {
            foreach ($categories as $category) {
                DB::table('categories')->insert($category);
            }

            foreach ($products as $product) {
                DB::table('master_items')->insert([
                    'item_code' => $product[0],
                    'item_name' => $product[1],
                    'ct_id' => $product[2],
                    'stock' => $product[3],
                    'hpp' => $product[4],
                    'sales_price' => $product[5],
                    'item_image' => $product[6],
                    'is_transactional' => $product[7],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
            DB::commit();
            
            $this->command->info('✅ Product seeder berhasil dijalankan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Log error
            // \Log::error('Product Seeder Error: ' . $e->getMessage(), [
            //     'file' => $e->getFile(),
            //     'line' => $e->getLine(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            
            // // Output ke console
            // $this->command->error('❌ Error pada Product Seeder:');
            // $this->command->error('Message: ' . $e->getMessage());
            // $this->command->error('File: ' . $e->getFile());
            // $this->command->error('Line: ' . $e->getLine());
            
            throw $e; // Re-throw untuk menghentikan proses
        }
       
    }
}
