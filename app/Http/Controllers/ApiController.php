<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FederalEntity;
use App\Models\Locality;
use App\Models\Municipality;
use App\Models\PostalCode;
use App\Models\Settlement;
use App\Models\SettlementType;
use App\Models\ZoneType;
use App\Models\City;
use App\Imports\ZipCodeImport;
use Maatwebsite\Excel\Facades\Excel;

class ApiController extends Controller
{
    function importData(){
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        $data = Excel::toCollection(new ZipCodeImport,'./CPdescarga.xls');
        $data->shift();
        foreach($data as $state => $state_info){
            $state_info->shift();
            foreach($state_info as $zip_code){
                if(!$estado = FederalEntity::where('name', $zip_code[4])->first())
                    $estado = new FederalEntity(['name' => $zip_code[4]]);
                $estado->save();
                if(!$municipio = Municipality::where('name', $zip_code[3])->first())
                    $municipio = new Municipality(['name' => $zip_code[3]]);
                $municipio->save();
                if(!$localidad = Locality::where('name', $zip_code[1])->first())
                    $localidad = new Locality(['name' => $zip_code[1]]);
                $localidad->save();
                if(!$tipo_colonia = SettlementType::where('name', $zip_code[2])->first())
                    $tipo_colonia = new SettlementType(['name' => $zip_code[2]]);
                $tipo_colonia->save();
                if(empty($zip_code[5])){
                    $ciudad_sn = 'Sin nombre';
                    if(!$ciudad = City::where('name', $ciudad_sn)->first())
                        $ciudad = new City(['name' => $ciudad_sn]);
                }
                else {
                    if(!$ciudad = City::where('name', $zip_code[5])->first())
                        $ciudad = new City(['name' => $zip_code[5]]);
                }
                $ciudad->save();
                if(!$zona = ZoneType::where('name', $zip_code[13])->first())
                    $zona = new ZoneType(['name' => $zip_code[13]]);
                $zona->save();
                
                if(!$codigo = PostalCode::where('zip_code', $zip_code[0])->first())
                    $codigo = new PostalCode(
                        [
                            'zip_code' => $zip_code[0], 
                            'locality_id' => $localidad->id, 
                            'city_id' => $ciudad->id, 
                            'federal_entity_id' => $estado->id, 
                            'municipality_id' => $municipio->id, 
                        ]
                    );
                $codigo->save();
                
                $colonia = new Settlement(
                        [
                            'name' => $zip_code[1],
                            'postal_code_id' => $codigo->id,
                            'zone_type_id' => $zona->id,
                            'settlement_type_id' => $tipo_colonia->id,
                        ]
                    );
                $colonia->save();
            }
        }
    }

    function getData($zip_code){
        $postalCodeInfo = PostalCode::with('settlements.settlement_type','federal_entity', 'municipality')
                                ->where('zip_code', $zip_code)
                                ->first();

        $response = [
            'status' => true,
            'payload' => [
                'id' => $postalCodeInfo->id,
                'code'=> $postalCodeInfo->zip_code,
                'municipality'=> $postalCodeInfo->municipality->name,
                'city'=> $postalCodeInfo->city->name,
                'state' => [
                    'id' => $postalCodeInfo->id,
                    'name' => $postalCodeInfo->federal_entity->name
                ]
            ]
        ];

        if(count($postalCodeInfo->settlements)  == 1){
            $response['payload']['settlement'] = $postalCodeInfo->settlements->first()->name;
            $response['payload']['settlement_type'] = $postalCodeInfo->settlements->first()->settlement_type->name;
            $response['payload']['zone'] = $postalCodeInfo->settlements->first()->zone_type;
        }
        else {
            foreach($postalCodeInfo->settlements as $settlement){
                $settlement_temp = [
                    'settlement' => $settlement->name,
                    'settlement_type' => $settlement->settlement_type->name,
                    'zone' => $settlement->zone_type  
                ];
                if(!isset($response['payload']['settlement']))
                    $response['payload']['settlement'] = [];

                $response['payload']['settlement'][] = $settlement_temp;
            }
        }

        return response()->json($response, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);
    }
}
