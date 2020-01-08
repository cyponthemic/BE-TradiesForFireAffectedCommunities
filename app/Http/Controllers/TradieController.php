<?php

namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Request as RequestAlias;

class TradieController extends Controller
{
    public function index(RequestAlias $request) {
        try {
            $client = \Algolia\AlgoliaSearch\SearchClient::create(
                'AVG184MB5R',
                env('ALGOLIA_SECRET', false)
            );

            $index = $client->initIndex('your_index_name');

            $index->setSettings([
                'searchableAttributes' => [
                    'Full Name',
                    'Postcode'
                ],
                'customRanking' => [
                    'desc(Postcode)'
                ]
            ]);

            $lat = $request->lat;
            $lon = $request->lon;
            $limit = $request->limit;

            if(!$request->lat || !$request->lon) {
                return $index->search('', [
                    'hitsPerPage' => 3000
                ]);
            }
            return $index->search('', [
                'aroundLatLng' => "$lat,$lon",
                'aroundRadius' => $limit,
                'hitsPerPage' => 3000,
                'filters' => 'Trade:'. $request->trade ?? ''
            ]);
        }

        catch (Exception $e) {
            return $e;
        }

        return 'success';
    }
}
