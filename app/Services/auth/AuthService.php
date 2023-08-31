<?php

namespace App\Services;

use App\Models\User;
use App\Models\Voters;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class AuthServices
{
    public function getUserPic(string $vin, string $voterCode):string
    {
        $vin = Crypt::encrypt($vin);
        $voterCode = Crypt::encrypt($voterCode);

        $voter = Voters::where('voter_no', $vin)
                    ->where('voter_code')
                    ->first();

        $user = $voter->user_id? User::find($voter->user_id) : '';
        
        if($user != ''){
            return $user->url;
        }
        return '';
    }

    public function isImageIdentical(string $k_image, string $unk_image):bool
    {
        $url = config('faceauthentiction');
        $body = [
            'k_image' => $k_image,
            'unk_image' => $unk_image
        ];

        $response = Http::post($url,$body);
        dd($response->collect());

        if($response->status() == 200){
            return $response->collect('result');
        }

        return false;
        
    }
}