<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ApiController extends Controller
{
    public function getData()
    {
        $data = [
            'response' => [
                'poNumber' => 'any po number',
                'shippingName' => Str::random(5),
                'shippingAddress1' => Str::random(20),
                'shippingAddress2' => Str::random(20),
                'shippingPostalCode' => rand(0, 99999),
                'shippingCity' => Str::random(5),
                'shippingCountry' => '',
                'shippingRegion' => '',
                'shippingMethod' => '',
                'clientSegment' => '',
                'items' => [
                    [
                        'sku' => 'any sku',
                        'brandId' => 'TRQ',
                        'quantity' => '1',
                    ],
                    [
                        'sku' => 'any sku',
                        'brandId' => 'TRQ',
                        'quantity' => '1',
                    ],
                ],
            ],
        ];

        return response()->json($data);
    }
}
