<?php

namespace App\Http\Controllers;

use App\DigitalId;
use Illuminate\Http\Request;

class DigitalIdController extends Controller
{
    private static function getPhoneByCard($card)
    {

    }

    public function postData(Request $request)
    {
        if ($request->has('did')) {
            $arResponse = DigitalId::getByDid($request->input('did'));

        } elseif ($request->has('card')) {
            $arData = self::getPhoneByCard($request->input('did'));

        } elseif ($request->has('email')
            || $request->has('phone')) {

            $arResponse = $this->verify($request);
        }

        return response()->json($arResponse);
    }


    /**
     * @param Request $request
     * @return mixed - id и склееные ids
     * @throws \Illuminate\Validation\ValidationException
     */
    private function verify(Request $request)
    {
        $this->validate($request, [
            'email' => 'email' //|unique:did
        ]);

        $email = ($request->input('email')?:false);
        $phone = ($request->input('phone')?:false);
        $rs = DigitalId::getByEAP($email, $phone);

        if(count($rs)==0) {

            if($email) {
//                return ['email:' => $email];
                $did = DigitalId::create([
                    'email' => $email,
                    'counter_up' => 0,
                    'created_at' => now()
                ]);
                return $did;
            }

            if($phone) {
//                return ['phone:' => $phone];
                $did = DigitalId::create([
                    'phone' => $phone,
                    'counter_up' => 0,
                    'created_at' => \Carbon::now()
                ]);
                return var_dump($did);
            }
        }

        return $rs;
    }

    /**
     * Get request to select did
     *
     * @param $id
     * @return array
     */
    public function showDid($id)
    {
        $arResponse = DigitalId::getByDid($id);
        return $arResponse;
    }

    public function root()
    {
        return ['error' => 'No parameters specified. Please send me params'];
    }
}