<?php
<<<<<<< HEAD
 
// fungsi untuk mengembalikan format rupiah dari suatu nominal tertentu
// dengan pemisah ribuan 
=======

// fungsi untuk mengembalikan format rupiah dari suatu nominal tertentu
// dengan pemisah ribuan
>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
function rupiah($nominal) {
    return "Rp ".number_format($nominal);
}

function dolar($nominal) {
    return "USD ".number_format($nominal);
}