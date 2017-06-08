<?php

/*
 * This file is part of the arch client php package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Liugj\Arch\Index;
use PHPUnit\Framework\TestCase;

class ArchTestCase extends TestCase
{
    public $index = null;

    public function setUp()
    {
        $options = [
         'access_app_id' => 'bs-a9b9d665cd94e436',
         'access_app_secret' => '345c78c76c290ed0',
         'timeout' => 10,
        ];
        $url = 'http://arch.local.boqii.com/v3.4/';
        $this->index = new Index($url, $options);
    }

    public function testSearch()
    {
        $param = ['keyword' => '狗粮', 'cateid' => 621, 'source' => 'app'];
        $s = 'price=price_app_v1&site_source=shop&brandid=0&cateid=622&attrid=&source=IOS&highlight=pname&facets=brandid%2Cc3%2Cp&format=json&range=price_app_v1%24%24';
        parse_str($s, $param);
        $response = $this->index->get($param);
        var_dump($response);
        $this->assertArrayHasKey('list', $response);
        $this->assertArrayHasKey('total', $response);
    }
}
