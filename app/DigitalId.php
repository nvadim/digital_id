<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DigitalId extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'did';
    protected $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'email'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @param int $did
     *
     * @return array - bonded ids, bonded emails, phone
     */
    public static function getByDid($did)
    {
        $did = intval($did);

        if(!$did)
            return ['error' => 'digital ID is not a number'];

        $data = DigitalId::where('pid', $did)->orderBy('id', 'desc')->take(10)->get();

        if(count($data)<=0) {
            return [];
        }

        $returnData = ['did' => $did, 'bonded' => [], 'emails' => [], 'phone' => ''];
        foreach ($data as $arItem) {
            $returnData['bonded'][] = $arItem['id'];
            if($arItem['email']) {
                $returnData['emails'][] = $arItem['email'];
            }

            if(empty($returnData['phone'])) {
                $returnData['phone'] = $arItem['phone'];
            }
        }

        return $returnData;
    }


    public static function getByEAP($email = false, $phone = false)
    {
        $returnData = false;

        if ($email && $phone):
            $res = DigitalId::where('email', $email)
                ->orWhere('phone', 'like', "%{$phone}%");
        elseif ($email):
            $res = DigitalId::where('email', $email);
        elseif ($phone):
            $res = DigitalId::where('phone', 'like', "%{$phone}%");
//            return count($res);
        endif;

        $returnData = $res->orderBy('id', 'desc')
            ->get();

        return $returnData; //$returnData->count()
    }

    /**
     * Verify
     * Get data
     *
     * @return pid, bounded ids
     */
    public static function getByEmail($email)
    {
        return DigitalId::where('email', '=', $email)->get();
    }

    public function getByPhone($phone)
    {

    }

}