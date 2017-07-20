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
        $query = array_intersect_key($param, array_flip([
                   'q', 'p', 'ps', 's', 'price', 'site_source', 'brandid', 'cateid', 'coupon',
                   'isstock', 'ifpromotion', 'isglobal', 'attrid', 'source', 'range',
                  ])
        );
        $query['highlight'] = 'pname';
        $query['facets'] = 'brandid,c3,p';
        $query['format'] = 'json';

        $response = $this->restClient->get('search', $query, $headers)->toArray();
        $fields = [
             'product' => [
                 'id' => 'id', 'pname' => 'pname', 'subtitle' => 'subtitle',
                 'sales' => 'sales',  'commentnum' => 'commentnum',
                 'stock' => 'inventory', 'upstatus' => 'upstatus', 'newcast' => 'newcast',
                 'isglobal' => 'isglobal', 'is_replace' => 'is_replace',
                 'globalstorage' => 'globalstorage', 'globalcity' => 'globalcity',
              ],
              'brand' => ['id' => 'brandid', 'brandname' => 'brandname'],
              'product_category' => ['cid' => 'c3'],
              'photo' => ['pid' => 'id', 'picpath' => 'picpath'],
        ];

        $response['result'] = array_map(function ($product) use ($fields) {
            $value = [];
            foreach ($fields as $key => $columns) {
                foreach ($columns as $keyCol => $column) {
                    $value[$key][$keyCol] = $product[$column] ?? '';
                }
            }

            return $value;
        }, $response['result']);

        return \Liugj\Helpers\array_key_exchange($response,
                [
                    'result' => 'list', 'total' => 'total', 'facets' => 'facets',
                    'crumbs' => 'crumbs', 'count' => 'count', 'keyword' => 'Keyword',
                ]
        );
    }
}
