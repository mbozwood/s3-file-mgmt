<?php

function norm_date($date) {
    return date('d-m-Y H:i:s', strtotime($date));
}

function is_admin() {
    return auth()->check() && auth()->user()->is_admin == 1;
}
