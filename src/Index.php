<?php

/*
 * This file is part of the arch client php package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liugj\Arch;

class Index
{
    /**
     * restClient.
     *
     * @var mixed
     */
    protected $restClient = null;

    /**
     * __construct.
     *
     * @param string $baseUri
     * @param array  $options
     *
     * @return mixed
     */
    public function __construct(string $baseUri, array $options = [])
    {
        $this->restClient = new RestClient($baseUri, $options);
    }

    /**
     * get.
     *
     * @param array $param
     * @param array $headers
     *
     * @return mixed
     */
    public function get(array $param, array $headers = [])
    {
        $fields = ['q','p','ps', 's','price', 'site_source', 'brandid','cateid', 
                   'isstock', 'ifpromotion', 'isglobal', 'attrid','source', 'range'
                  ];
        $query = array_intersect_key($param, array_flip($fields));
        $query['highlight'] = 'pname';
        $query['facets'] = 'brandid,c3,p';
        $query['format'] = 'json';

        $response = $this->restClient->get('search', $query, $headers)->toArray();
        $response['result'] = array_map(function ($product) {
            $value = [
                'product' => ['id' => $product['id'], 'pname' => $product['pname'],
                'subtitle' => $product['subtitle'] ?? '', 'sales' => $product['sales'],
                'commentnum' => $product['commentnum'], 'stock' => $product['inventory'],
                'upstatus' => $product['upstatus'], 'cast' => $product['cast'],
                'newcast' => $product['newcast'], 'isglobal' => $product['isglobal'],
                'is_replace' => $product['is_replace'], 'globalstorage' => $product['globalstorage'],
                'globalcity' => $product['globalcity']??'',
                ],
                'brand' => ['id' => $product['brandid'], 'name' => $product['brandname']],
                'product_category' => ['cid' => $product['c3']],
                'photo' => ['pid' => $product['id'], 'picpath' => $product['picpath']],
                ];

            return $value;
        }, $response['result']);

        return \Liugj\Helpers\array_key_exchange($response,
                ['result' => 'list', 'total' => 'total', 'facets' => 'facets', 'crumbs' => 'crumbs','count'=>'count']
        );
    }
}
