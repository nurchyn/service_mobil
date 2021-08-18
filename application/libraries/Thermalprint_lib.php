<?php defined('BASEPATH') or exit('No direct script access allowed');
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class Thermalprint_lib
{
	public function cek()
    {
        $connector = new FilePrintConnector("php://stdout");
        $printer = new Printer($connector);
        $printer -> text("Hello World!\n");
        $printer -> cut();
        $printer -> close();
    }
    public function pos58()
    {
        $connector = new WindowsPrintConnector('POS-58');
        $printer = new Printer($connector);
        $printer -> text("Hello World!\n");
        $printer -> cut();
        $printer -> close();
    }

   
    public function cek_cetak($data_user=null, $data_profile=null, $data_penjualan=null) {

        $tgl_trans = date('d-m-Y H:i:s', strtotime($data_penjualan[0]->created_at));
        // membuat connector printer ke shared printer bernama "printer_a" (yang telah disetting sebelumnya)
        $connector = new WindowsPrintConnector("POS-58");
    
        // membuat objek $printer agar dapat di lakukan fungsinya
        $printer = new Printer($connector);
    
        // membuat fungsi untuk membuat 1 baris tabel, agar dapat dipanggil berkali-kali dgn mudah
        function buatBaris2Kolom($kolom1, $kolom2) {
            // Mengatur lebar setiap kolom (dalam satuan karakter)
            $lebar_kolom_1 = 20;
            $lebar_kolom_2 = 8;
               
            // Melakukan wordwrap(), jadi jika karakter teks melebihi lebar kolom, ditambahkan \n 
            $kolom1 = wordwrap($kolom1, $lebar_kolom_1, "\n", true);
            $kolom2 = wordwrap($kolom2, $lebar_kolom_2, "\n", true);
            
    
            // Merubah hasil wordwrap menjadi array, kolom yang memiliki 2 index array berarti memiliki 2 baris (kena wordwrap)
            $kolom1Array = explode("\n", $kolom1);
            $kolom2Array = explode("\n", $kolom2);
           
    
            // Mengambil jumlah baris terbanyak dari kolom-kolom untuk dijadikan titik akhir perulangan
            $jmlBarisTerbanyak = max(count($kolom1Array), count($kolom2Array));
            
            // Mendeklarasikan variabel untuk menampung kolom yang sudah di edit
            $hasilBaris = array();
    
            // Melakukan perulangan setiap baris (yang dibentuk wordwrap), untuk menggabungkan setiap kolom menjadi 1 baris 
            for ($i = 0; $i < $jmlBarisTerbanyak; $i++) {
    
                // memberikan spasi di setiap cell berdasarkan lebar kolom yang ditentukan, 
                $hasilKolom1 = str_pad((isset($kolom1Array[$i]) ? $kolom1Array[$i] : ""), $lebar_kolom_1, " ");
                $hasilKolom2 = str_pad((isset($kolom2Array[$i]) ? $kolom2Array[$i] : ""), $lebar_kolom_2, " ");
    
                // Menggabungkan kolom tersebut menjadi 1 baris dan ditampung ke variabel hasil (ada 1 spasi disetiap kolom)
                $hasilBaris[] = $hasilKolom1 . " " . $hasilKolom2;
            }

            // Hasil yang berupa array, disatukan kembali menjadi string dan tambahkan \n disetiap barisnya.
            return implode("\n", $hasilBaris) . "\n";
        }   
    
        // Membuat judul
        $printer->initialize();
        $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT); // Setting teks menjadi lebih besar
        $printer->setJustification(Printer::JUSTIFY_CENTER); // Setting teks menjadi rata tengah
        $printer->text($data_profile->nama."\n");
        $printer->text("\n");

        // Alamat
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($data_profile->alamat."\n");
        $printer->text($data_profile->kota."\n");
        $printer->text("\n");
    
        // Data transaksi
        $printer->initialize();
        $printer->text("Kasir : ".$data_user[0]->nama."\n");
        $printer->text("Waktu : ".$tgl_trans."\n");
    
        // Membuat tabel
        $printer->initialize(); // Reset bentuk/jenis teks
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text("-------------------------------\n");
        $printer->text(buatBaris2Kolom("Layanan", "Harga",));
        $printer->text("-------------------------------\n");

        foreach ($data_penjualan as $key => $value) {
            $printer->text(buatBaris2Kolom($value->nama, number_format($value->harga_satuan,0,',','.')));    
        }

        $printer->text("-------------------------------\n");
        $printer->text(buatBaris2Kolom("Total", number_format($data_penjualan[0]->harga_total,0,',','.')));
        $printer->text("-------------------------------\n");
        $printer->text(buatBaris2Kolom("Pembayaran", number_format($data_penjualan[0]->harga_bayar,0,',','.')));
        $printer->text("\n");
        $printer->text(buatBaris2Kolom("Kembalian", number_format($data_penjualan[0]->harga_kembalian,0,',','.')));
        $printer->text("\n");
    
            // Pesan penutup
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Terima kasih\n");
        $printer->text("Atas Kepercayaan Anda\n");
    
        $printer->feed(3); // mencetak 5 baris kosong agar terangkat (pemotong kertas saya memiliki jarak 5 baris dari toner)
        $printer->close();
    }
    
    
}



