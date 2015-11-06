<?php
/**
 * Date: 15/10/13
 * Time: 20:04
 */

namespace Hoimi;


interface Gettable {
    public function get($key, $default = null);
}