<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Result;

class Digitalid extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'did';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'email', 'pid'
    ];
    public $timestamps = false;

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

        if (!$did)
            return ['error' => 'digital ID is not a number'];

        $data = Digitalid::where('pid', $did)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        if (count($data) <= 0) {
            return [];
        }

        $returnData = ['did' => $did, 'bonded' => [], 'emails' => [], 'phone' => ''];
        foreach ($data as $arItem) {
            $returnData['bonded'][] = $arItem['id'];
            if ($arItem['email']) {
                $returnData['emails'][] = $arItem['email'];
            }

            if (empty($returnData['phone'])) {
                $returnData['phone'] = $arItem['phone'];
            }
        }

        return $returnData;
    }


    public static function getByEAP($email = false, $phone = false)
    {
        $returnData = false;

        if ($email && $phone):
            $res = self::where('email', $email)
                ->orWhere('phone', 'like', "%{$phone}%");
        elseif ($email):
            $res = self::where('email', $email);
        elseif ($phone):
            $res = self::where('phone', 'like', "%{$phone}%");
        endif;

        $returnData = $res->get()->toArray();

        return $returnData;
    }

    /**
     * Отформатированные данные
     *
     * @param array $arr
     * @return array: [
     *    'email' => ['id' => 123, pid => 12, counter_up => 0]
     *    'phone' => ['id' => 123, pid => 12]
     * ]
     */
    public static function formatData(array $arr)
    {
        $selectedItems = [];
        foreach ($arr as $dbItem) {
            if(!empty($dbItem['phone'])) {
                $selectedItems['phone'] = [
                    'id' => $dbItem['id'],
                    'pid' => $dbItem['pid']
                ];
                continue;
            }

            if(!empty($dbItem['email'])) {
                $selectedItems['email'] = [
                    'id' => $dbItem['id'],
                    'pid' => $dbItem['pid'],
                    'counter_up' => $dbItem['counter_up']
                ];
            }
        }

        return $selectedItems;
    }

    public static function createEmailEntity($email)
    {
        if(!$email)
            return false;

        $didWE = self::create([
            'email' => $email,
            'counter_up' => 0,
        ]);

        return $didWE;
    }


    public static function createPhoneEntity($phone)
    {
        if(!$phone)
            return false;

        $didWP = self::create([
            'phone' => $phone,
            'counter_up' => 0
        ]);

        return $didWP;
    }

}