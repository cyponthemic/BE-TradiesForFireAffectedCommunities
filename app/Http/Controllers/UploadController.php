<?php
// composer autoload
namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request as RequestAlias;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    /**
     * @param RequestAlias $request
     * @return \Exception|Exception|string
     * @throws \Algolia\AlgoliaSearch\Exceptions\MissingObjectId
     */
    public function export(RequestAlias $request) {
        try {
            $client = \Algolia\AlgoliaSearch\SearchClient::create(
                'AVG184MB5R',
                env('ALGOLIA_SECRET', false)
            );

            $index = $client->initIndex('your_index_name');
            $index->setSettings([
                'searchableAttributes' => [
                    'Trade',
                ]
            ]);

            $index->setSettings([
                'disableExactOnAttributes' => [
                    'Trade',
                ]
            ]);

            $index->saveObjects($request->tradies, ['autoGenerateObjectIDIfNotExist' => true]);
        }

        catch (Exception $e) {
            return $e;
        }

        return 'success';
    }
}
