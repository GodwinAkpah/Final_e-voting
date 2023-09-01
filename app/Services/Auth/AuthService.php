<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Voters;
use COM;
use Crypt;
use CURLFile;
use Exception;
use Illuminate\Support\Facades\Http;

class AuthService
{
    public function getUserPic(string $vin):string
    {
        $user =User::where('voter_no',$vin)
                    ->first();

        if(!$user){
            throw new Exception('No user exist with this voters number');
        }
        
        return $user->profile_img;
    }

    public function isImageIdentical(string $k_image, string $unk_image):bool
    {
        $url = config('faceauthentication');

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'=> 'k_image',
                    'filename' => 'known.jpg',
                    'contents' => fopen($k_image,'r')
                ],
                [
                    'name' => 'unk_image',
                    'filename' => 'unknown.jpg',
                    'contents' => fopen($unk_image, 'r')
                ],
            ]
        ]);

        
        if($response->getStatusCode() == 200){
            $result = json_decode($response->getBody('result'),true);
            return $result['result'];
        }

        return false;
        
    }
}