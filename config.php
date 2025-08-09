<?php
// config.php

$koneksi = mysqli_connect("localhost", "root", "", "apotek1");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
