<?php

namespace GrpcServerService\Base;

use GrpcServerModel\Base\UserdataInterface;
use GrpcServerModel\Base\GetUserRequest;
use GrpcServerModel\Base\GetUserResponse;

use Spiral\RoadRunner\GRPC\ContextInterface;

class UserdataService implements UserdataInterface
{

    public function GetUser(ContextInterface $ctx, GetUserRequest $in): GetUserResponse
    {
        $out = new GetUserResponse();

        $userdata = array(
            "employee_uuid"                 => "06ba45c5-77db-a1fd-0153-5a86e727a261",
            "employee_npk"                  => "11000141",
            "employee_name"                 => "Prayuda Wiguna",
            "employee_email"                => "prayuda@biofarma.co.id",
            "employee_entity_uuid"          => "5602afe3-cbcd-fb7c-14b9-349c341d58b1",
            "employee_entity_number"	    => "10000000",
            "employee_entity_code"	        => "BIOF",
            "employee_entity_name"	        => "PT Bio Farma (Persero)",
            "employee_position_uuid"	    => "f59e1a91-f8a9-5d33-694a-6fdae746c427",
            "employee_position_code"	    => "43",
            "employee_position_name"	    => "Senior Officer",
            "employee_organization_uuid"	=> "2d559945-87fe-4b1c-4661-937893b49019",
            "employee_organization_code"	=> "02040303",
            "employee_organization_name"	=> "Rekayasa Perangkat Lunak",
            "employee_band_uuid"	        => "xxx59945-87fe-4b1c-4661-937893b49019",
            "employee_band_code"	        => "III",
            "employee_band_name"	        => "Band III"
        );

        return $out->setResponse(json_encode($userdata));
    }
}
