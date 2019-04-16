<?php

namespace App\Http\Controllers;

use App\Digitalid;
use Illuminate\Http\Request;

class DigitalIdController extends Controller
{
    private static function getPhoneByCard($card)
    {

    }

    public function postData(Request $request)
    {
        if ($request->has('did')) {
            $arResponse = Digitalid::getByDid($request->input('did'));

        } elseif ($request->has('card')) {
            $arData = self::getPhoneByCard($request->input('did'));

        } elseif ($request->has('email')
            || $request->has('phone')) {

            $arResponse = self::verify($request);
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
        $returnData = [];
        $flag = [
            'phone' => ['created' => 0],
        ];

        $reqEmail = ($request->input('email') ?: false);
        $reqPhone = ($request->input('phone') ?: false);

        $dbItems = Digitalid::getByEAP($reqEmail, $reqPhone);
        $existsItems = Digitalid::formatData($dbItems);


        // PHONE
        if(isset($existsItems['phone'])) {
            $returnData['did'] = $existsItems['phone']['id'];
        } elseif($reqPhone) {
            $res = Digitalid::createPhoneEntity($reqPhone);
            $res->pid = $res->id;
            $res->save();
            $flag['phone']['created'] = 1;

            $returnData['did'] = $res->id;
        }

        // EMAIL
        if(isset($existsItems['email'])) {
            $emailId = $existsItems['email']['id'];
            $returnData['did'] = isset($returnData['did'])? $returnData['did']:$existsItems['email']['pid'];

            if($reqPhone) {
                $arData = Digitalid::find($emailId);

                if($arData->pid!=$returnData['did']) {
                    $arData->pid = $returnData['did'];
                    $arData->counter_up = ++$arData->counter_up;
                    $arData->save();
                }
            }

        } elseif($reqEmail) {
            $res = Digitalid::createEmailEntity($reqEmail);
            $returnData['did'] = isset($returnData['did'])? $returnData['did']:$res->id;

            $res->pid = $returnData['did'];
            $res->save();
        }

        $data = Digitalid::where('pid', $returnData['did'])
            ->where('id', '!=', $returnData['did'])
            ->get()->toArray();

        foreach ($data as $item) {
            $returnData['bonded'][] = $item['id'];
        }

        return $returnData;
    }

    /**
     * Get request to select did
     *
     * @param $id
     * @return array
     */
    public function showDid($id)
    {
        $arResponse = Digitalid::getByDid($id);
        return $arResponse;
    }

    public function root()
    {
        return ['error' => 'No parameters specified. Please send me params'];
    }

    public function test()
    {
    /*
        $arData = Digitalid::find(8);

        $arData->pid = 5;
        $arData->counter_up = ++$arData->counter_up;
        $arData->save();
    */

    /*
        $rs = DB::table('did')
            ->where("id", '=', 8);

        $data = $rs->get();
    */

    /*
        $data->pid = 8;
        $data->counter_up = ++$data->counter_up;
        $data->save();
    */

        $res = Digitalid::where('pid', 5)
            ->where('id', '!=', 3)
            ->where('id', '!=', 5)
            ->get();
        return $res;
    }
}