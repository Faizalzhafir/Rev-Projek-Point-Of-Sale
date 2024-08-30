<?php

function format_uang ($angka) {
    return number-format($angka, 0, ',', '.');
}