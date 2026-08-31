<?php
header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "status" => "Online",
    "message" => "Selamat datang di REST API Pengelolaan Inventory",
    "endpoints" => [
        "barang" => "http://localhost/pengelolahan_inventory/controllers/barang.php",
        "gudang" => "http://localhost/pengelolahan_inventory/controllers/gudang.php",
        "vendor" => "http://localhost/pengelolahan_inventory/controllers/vendor.php"
    ]
], JSON_PRETTY_PRINT);
?>