<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Makanan Ringan (30 items)
            ['IND-001', 'Indomie Goreng', 50, 3000, 'indomie.jpg', 1],
            ['IND-002', 'Indomie Soto', 45, 3000, 'indomie_soto.jpg', 1],
            ['SED-001', 'Sedap Kari', 40, 3000, 'sedap.jpg', 1],
            ['SED-002', 'Sedap Ayam Bawang', 35, 3000, 'sedap_ayam.jpg', 1],
            ['CHI-001', 'Chitato Original', 60, 12000, 'chitato.jpg', 1],
            ['LAY-001', 'Lays Rumput Laut', 55, 12000, 'lays.jpg', 1],
            ['PIL-001', 'Pilus Garuda', 70, 5000, 'pilus.jpg', 1],
            ['CHE-001', 'Cheetos Balado', 65, 10000, 'cheetos.jpg', 1],
            ['TAN-001', 'Tango Wafer', 50, 8000, 'tango.jpg', 1],
            ['BEN-001', 'Beng-Beng', 80, 5000, 'bengbeng.jpg', 1],
            ['SIL-001', 'Silverqueen', 40, 15000, 'silverqueen.jpg', 1],
            ['CHA-001', 'Chacha Pedas', 75, 5000, 'chacha.jpg', 1],
            ['NIS-001', 'Nissin Crackers', 60, 7000, 'nissin.jpg', 1],
            ['ORI-001', 'Oreo Original', 50, 10000, 'oreo.jpg', 1],
            ['MAL-001', 'Malkist Roma', 45, 8000, 'malkist.jpg', 1],
            ['NUT-001', 'Nutella Biscuit', 30, 12000, 'nutella.jpg', 1],
            ['KAC-001', 'Kacang Atom', 60, 10000, 'kacang_atom.jpg', 1],
            ['KAC-002', 'Kacang Telur', 55, 8000, 'kacang_telur.jpg', 1],
            ['KAC-003', 'Kacang Panjang', 50, 7000, 'kacang_panjang.jpg', 1],
            ['PER-001', 'Permen Kopiko', 100, 500, 'kopiko.jpg', 1],
            ['PER-002', 'Permen Mintz', 120, 500, 'mintz.jpg', 1],
            ['PER-003', 'Permen Yosan', 90, 500, 'yosan.jpg', 1],
            ['PER-004', 'Permen Foxs', 80, 500, 'foxs.jpg', 1],
            ['PER-005', 'Permen Nano-Nano', 70, 500, 'nano.jpg', 1],
            ['CIL-001', 'Cilok Kuah', 40, 8000, 'cilok.jpg', 1],
            ['BAS-001', 'Basreng Pedas', 35, 10000, 'basreng.jpg', 1],
            ['SEB-001', 'Seblak Kerupuk', 30, 12000, 'seblak.jpg', 1],
            ['KER-001', 'Kerupuk Udang', 50, 5000, 'kerupuk_udang.jpg', 1],
            ['KER-002', 'Kerupuk Bawang', 60, 5000, 'kerupuk_bawang.jpg', 1],
            ['KER-003', 'Kerupuk Ikan', 45, 6000, 'kerupuk_ikan.jpg', 1],
            
            // Minuman (30 items)
            ['AQU-001', 'Aqua 600ml', 100, 3000, 'aqua.jpg', 1],
            ['LEM-001', 'Le Minerale 600ml', 90, 3000, 'leminerale.jpg', 1],
            ['VIT-001', 'Vit 600ml', 80, 3000, 'vit.jpg', 1],
            ['NES-001', 'Nestle Pure Life', 70, 3000, 'nestle.jpg', 1],
            ['COC-001', 'Coca Cola 350ml', 85, 6000, 'cocacola.jpg', 1],
            ['SPR-001', 'Sprite 350ml', 75, 6000, 'sprite.jpg', 1],
            ['FAN-001', 'Fanta 350ml', 65, 6000, 'fanta.jpg', 1],
            ['POC-001', 'Pocari Sweat', 60, 10000, 'pocari.jpg', 1],
            ['MIL-001', 'Mizone', 55, 8000, 'mizone.jpg', 1],
            ['TEH-001', 'Teh Botol', 70, 5000, 'tehbotol.jpg', 1],
            ['TEH-002', 'Teh Pucuk', 65, 5000, 'tehpucuk.jpg', 1],
            ['TEH-003', 'Teh Kotak', 60, 6000, 'tehkotak.jpg', 1],
            ['FRE-001', 'Freshtea', 50, 6000, 'freshtea.jpg', 1],
            ['COP-001', 'Good Day Freeze', 45, 8000, 'goodday.jpg', 1],
            ['KOP-001', 'Kopiko 78', 40, 8000, 'kopiko78.jpg', 1],
            ['KOP-002', 'Kopi Kapal Api', 35, 5000, 'kapalapi.jpg', 1],
            ['KOP-003', 'Kopi ABC', 30, 5000, 'kopiabc.jpg', 1],
            ['SUS-001', 'Ultra Milk', 50, 7000, 'ultra.jpg', 1],
            ['SUS-002', 'Indomilk', 45, 6000, 'indomilk.jpg', 1],
            ['SUS-003', 'Dancow', 40, 8000, 'dancow.jpg', 1],
            ['JUS-001', 'Buavita Jeruk', 35, 10000, 'buavita.jpg', 1],
            ['JUS-002', 'Floridina', 30, 10000, 'floridina.jpg', 1],
            ['JUS-003', 'Yakult', 60, 3000, 'yakult.jpg', 1],
            ['ENE-001', 'Extra Joss', 70, 2000, 'extrajoss.jpg', 1],
            ['ENE-002', 'Hemaviton', 65, 3000, 'hemaviton.jpg', 1],
            ['ENE-003', 'Kuku Bima', 60, 3000, 'kukubima.jpg', 1],
            ['SIR-001', 'Marjan Sirup', 25, 15000, 'marjan.jpg', 1],
            ['SIR-002', 'ABC Sirup', 30, 12000, 'abcsirup.jpg', 1],
            ['SIR-003', 'Bendera Sirup', 20, 10000, 'bendera.jpg', 1],
            
            // Bahan Pokok (30 items)
            ['BER-001', 'Beras Ramos 5kg', 20, 60000, 'beras_ramos.jpg', 1],
            ['BER-002', 'Beras Setra Ramos 5kg', 15, 70000, 'beras_setra.jpg', 1],
            ['BER-003', 'Beras IR 64 5kg', 18, 55000, 'beras_ir64.jpg', 1],
            ['GUL-001', 'Gula Pasir 1kg', 25, 15000, 'gula_pasir.jpg', 1],
            ['GUL-002', 'Gula Merah 500gr', 30, 10000, 'gula_merah.jpg', 1],
            ['GUL-003', 'Gula Jawa 500gr', 20, 12000, 'gula_jawa.jpg', 1],
            ['TEL-001', 'Telur Ayam 1kg', 15, 25000, 'telur.jpg', 1],
            ['TEL-002', 'Telur Bebek 1kg', 10, 35000, 'telur_bebek.jpg', 1],
            ['MIN-001', 'Minyak Goreng 1L', 30, 20000, 'minyak.jpg', 1],
            ['MIN-002', 'Minyak Goreng 2L', 20, 38000, 'minyak_2l.jpg', 1],
            ['MIN-003', 'Minyak Goreng Premium', 15, 25000, 'minyak_premium.jpg', 1],
            ['TER-001', 'Tepung Terigu 1kg', 25, 12000, 'terigu.jpg', 1],
            ['TER-002', 'Tepung Beras 1kg', 20, 15000, 'tepung_beras.jpg', 1],
            ['TER-003', 'Tepung Tapioka 500gr', 30, 8000, 'tapioka.jpg', 1],
            ['GAR-001', 'Garam Halus 500gr', 40, 5000, 'garam.jpg', 1],
            ['GAR-002', 'Garam Krosok 1kg', 25, 8000, 'garam_krosok.jpg', 1],
            ['MER-001', 'Mentega 250gr', 20, 15000, 'mentega.jpg', 1],
            ['MER-002', 'Margarin 250gr', 15, 12000, 'margarin.jpg', 1],
            ['SUS-004', 'Susu Kental Manis', 35, 10000, 'skm.jpg', 1],
            ['SUS-005', 'Susu Bubuk 400gr', 20, 30000, 'susu_bubuk.jpg', 1],
            ['KEC-001', 'Kecap Manis 275ml', 30, 10000, 'kecap.jpg', 1],
            ['KEC-002', 'Kecap Asin 150ml', 25, 8000, 'kecap_asin.jpg', 1],
            ['SAO-001', 'Saori Saus Tiram', 20, 12000, 'saori.jpg', 1],
            ['SAM-001', 'Sambal ABC', 40, 8000, 'sambal_abc.jpg', 1],
            ['SAM-002', 'Sambal Terasi', 35, 7000, 'sambal_terasi.jpg', 1],
            ['MIE-001', 'Mie Kuning', 50, 5000, 'mie_kuning.jpg', 1],
            ['MIE-002', 'Mie Keriting', 45, 6000, 'mie_keriting.jpg', 1],
            ['BAK-001', 'Bakso Sapi 500gr', 15, 25000, 'bakso.jpg', 1],
            ['SOS-001', 'Sosis Ayam', 20, 20000, 'sosis.jpg', 1],
            ['NUG-001', 'Nugget Ayam', 15, 25000, 'nugget.jpg', 1],
            
            // Perlengkapan Rumah Tangga (30 items)
            ['SAB-001', 'Sabun Mandi Lifebuoy', 60, 5000, 'lifebuoy.jpg', 1],
            ['SAB-002', 'Sabun Mandi Dove', 50, 7000, 'dove.jpg', 1],
            ['SAB-003', 'Sabun Mandi Lux', 55, 6000, 'lux.jpg', 1],
            ['SAM-003', 'Shampoo Clear', 40, 12000, 'clear.jpg', 1],
            ['SAM-004', 'Shampoo Sunsilk', 35, 10000, 'sunsilk.jpg', 1],
            ['SAM-005', 'Shampoo Pantene', 30, 15000, 'pantene.jpg', 1],
            ['SIL-002', 'Silk Pemutih', 25, 10000, 'silk.jpg', 1],
            ['SOK-001', 'So Klin Pewangi', 20, 12000, 'soklin.jpg', 1],
            ['SOF-001', 'Softener Molto', 25, 10000, 'molto.jpg', 1],
            ['SAB-004', 'Sabun Cuci Rinso', 30, 12000, 'rinso.jpg', 1],
            ['SAB-005', 'Sabun Cuci Daia', 25, 10000, 'daia.jpg', 1],
            ['PEM-001', 'Pemutih Bayclin', 20, 8000, 'bayclin.jpg', 1],
            ['PAS-001', 'Pasta Gigi Pepsodent', 40, 10000, 'pepsodent.jpg', 1],
            ['PAS-002', 'Pasta Gigi Close Up', 35, 8000, 'closeup.jpg', 1],
            ['PAS-003', 'Pasta Gigi Sensodyne', 30, 15000, 'sensodyne.jpg', 1],
            ['SIS-001', 'Sikat Gigi Formula', 50, 5000, 'formula.jpg', 1],
            ['SIS-002', 'Sikat Gigi Cussons', 45, 6000, 'cussons.jpg', 1],
            ['HAR-001', 'Harpic Pembersih Toilet', 15, 20000, 'harpic.jpg', 1],
            ['VIC-001', 'Vixal Pembersih Lantai', 20, 15000, 'vixal.jpg', 1],
            ['TIS-001', 'Tisu Gulung', 30, 5000, 'tisu_gulung.jpg', 1],
            ['TIS-002', 'Tisu Wajah Paseo', 25, 8000, 'paseo.jpg', 1],
            ['TIS-003', 'Tisu Basah Nice', 20, 10000, 'nice.jpg', 1],
            ['PEM-002', 'Pembalut Laurier', 25, 12000, 'laurier.jpg', 1],
            ['PEM-003', 'Pembalut Charm', 20, 15000, 'charm.jpg', 1],
            ['POP-001', 'Popok Bayi Sweety', 15, 30000, 'sweety.jpg', 1],
            ['POP-002', 'Popok Bayi Merries', 10, 40000, 'merries.jpg', 1],
            ['KOR-001', 'Korek Api Gas', 50, 2000, 'korek.jpg', 1],
            ['OBR-001', 'Obat Nyamuk Baygon', 30, 10000, 'baygon.jpg', 1],
            ['OBR-002', 'Obat Nyamuk Hit', 25, 8000, 'hit.jpg', 1],
            ['OBR-003', 'Obat Nyamuk Autan', 20, 12000, 'autan.jpg', 1],
            
            // Alat Tulis (10 items)
            ['PEN-001', 'Pulpen Standard', 100, 3000, 'pulpen.jpg', 1],
            ['PEN-002', 'Pulpen Pilot', 80, 5000, 'pilot.jpg', 1],
            ['PEN-003', 'Pensil 2B', 90, 2000, 'pensil.jpg', 1],
            ['PEN-004', 'Penghapus Faber', 70, 3000, 'penghapus.jpg', 1],
            ['PEN-005', 'Penggaris 30cm', 60, 5000, 'penggaris.jpg', 1],
            ['BUK-001', 'Buku Tulis 38 Lbr', 50, 5000, 'buku.jpg', 1],
            ['BUK-002', 'Buku Gambar A4', 40, 8000, 'buku_gambar.jpg', 1],
            ['LEM-002', 'Lem UHU', 30, 7000, 'lem.jpg', 1],
            ['GUN-001', 'Gunting Kertas', 25, 10000, 'gunting.jpg', 1],
            ['STE-001', 'Stapler Standard', 20, 15000, 'stapler.jpg', 1],
            
            // Lain-lain (10 items)
            ['BAT-001', 'Baterai ABC', 50, 5000, 'baterai.jpg', 1],
            ['BOL-001', 'Bola Lampu Philips', 30, 10000, 'lampu.jpg', 1],
            ['KUN-001', 'Kunci Ring Set', 15, 25000, 'kunci.jpg', 1],
            ['SEL-001', 'Selotip Besar', 40, 5000, 'selotip.jpg', 1],
            ['PLA-001', 'Plastik Klip 25x38', 35, 8000, 'plastik.jpg', 1],
            ['KAR-001', 'Karet Gelang', 60, 2000, 'karet.jpg', 1],
            ['BAN-001', 'Ban Dalam Sepeda', 10, 30000, 'ban.jpg', 1],
            ['PIS-001', 'Pisau Cutter', 25, 7000, 'cutter.jpg', 1],
            ['JAR-001', 'Jarum Benang', 45, 3000, 'jarum.jpg', 1],
            ['BEN-002', 'Benang Jahit', 30, 5000, 'benang.jpg', 1]
        ];

        foreach ($products as $product) {
            DB::table('master_items')->insert([
                'item_code' => $product[0],
                'item_name' => $product[1],
                'stock' => $product[2],
                'sales_price' => $product[3],
                'item_image' => $product[4],
                'is_transactional' => $product[5],
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
