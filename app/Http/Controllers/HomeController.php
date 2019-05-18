<?php

namespace App\Http\Controllers;

use DataTables;

class HomeController extends Controller {

    public function getData()
    {
        try
        {
            $data = file_get_contents("https://data.nasa.gov/resource/gh4g-9sfh.json");

            $jsonArray = json_decode($data, true);

            return DataTables::of($jsonArray)->make(true);
        } catch(\Exception $e){
            return [];
        }
    }

    public function home()
    {
        $json = file_get_contents("https://data.nasa.gov/resource/gh4g-9sfh.json");

        $data = json_decode($json);
        
        $c = collect($data);
        $weightData = $c->groupBy('mass')
            ->mapToGroups(function ($item, $key) {
                return [
                    $key => [
                        'count' => $item->where('mass', $key)->count(),
                        'weight' => @($key / 1000) . "kg"
                    ]
                ];
            })->flatten(1);

        $yearData = $c->sortBy('year')->groupBy('year')
            ->mapToGroups(function ($item, $key) {
                return [
                    $key => [
                        'count' => $item->where('year', $key)->count(),
                        'year' => substr($key, 0, 4)
                    ]
                ];
            })->flatten(1);

        
        return view('home')->with(compact('data','yearData', 'weightData'));
    }

}