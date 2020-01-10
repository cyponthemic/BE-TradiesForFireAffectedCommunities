<?php

namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Request as RequestAlias;

class TradieController extends Controller
{
    public function index(RequestAlias $request) {
        $correct_password = env('FIND_PASSWORD');

        try {
            $client = \Algolia\AlgoliaSearch\SearchClient::create(
                'AVG184MB5R',
                env('ALGOLIA_SECRET', false)
            );

            $index = $client->initIndex('your_index_name');

            $index->setSettings([
                'searchableAttributes' => [
                    'Trade'
                ],
                'customRanking' => [
                    'desc(Postcode)'
                ],
                'searchableAttributes' => [
                    'Trade'
                ],
                'disableExactOnAttributes' => [
                    'Trade'
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
            return $index->search($request->searchQuery ?? '', [
                'aroundLatLng' => "$lat,$lon",
                'aroundRadius' => $limit,
                'hitsPerPage' => 3000,
                'filters' => $request->trade ? 'Trade:'. $request->trade ?? '' : '',
                'attributesToRetrieve' => [
                    'Timestamp',
                    'Full Name',
                    'Company Name',
                    'Trade',
                    '_geoloc',
                    'Postcode',
                    $request->password === $correct_password ? 'Email Address' : '',
                    $request->password === $correct_password ? 'Phone Number' : ''
                ]
            ]);
        }

        catch (Exception $e) {
            return $e;
        }

        return 'success';
    }
}
