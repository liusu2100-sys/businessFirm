<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'account_no', 'login_type', 'account_pwd', 'status',
        'hav_coin', 'insurance_box', 'stamina_level', 'weight_level', 'fh_level',
        'kd_value', 'awm_ammo', 'helmet_l6', 'armor_l6', 'slot9_card',
        'melee_skins', 'weapon_skins', 'operator_skins', 'train_six_grid',
        'trade_start_time', 'trade_end_time', 'ban_90_days', 'common_login_area',
        'rank_level', 'price', 'deposit', 'rental_duration', 'remark',
        'price_ratio', 'rent_days', 'face_is_self', 'daily_consume', 'pic',
        'liquid_assets', 'screenshots', 'view_count',
    ];

    protected $casts = [
        'train_six_grid' => 'boolean',
        'ban_90_days' => 'boolean',
        'face_is_self' => 'boolean',
        'price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'kd_value' => 'decimal:2',
        'daily_consume' => 'decimal:2',
        'liquid_assets' => 'decimal:2',
        'price_ratio' => 'decimal:2',
        'melee_skins' => 'array',
        'weapon_skins' => 'array',
        'operator_skins' => 'array',
        'screenshots' => 'array',
    ];

    protected $hidden = ['account_pwd'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function toApiArray(bool $showSecret = false): array
    {
        $data = $this->toArray();
        // camelCase for frontend
        $map = [
            'account_no' => 'accountNo', 'login_type' => 'loginType',
            'hav_coin' => 'havCoin', 'insurance_box' => 'insuranceBox',
            'stamina_level' => 'staminaLevel', 'weight_level' => 'weightLevel',
            'fh_level' => 'fhLevel', 'kd_value' => 'kdValue', 'awm_ammo' => 'awmAmmo',
            'helmet_l6' => 'helmetL6', 'armor_l6' => 'armorL6', 'slot9_card' => 'slot9Card',
            'melee_skins' => 'meleeSkins', 'weapon_skins' => 'weaponSkins',
            'operator_skins' => 'operatorSkins', 'train_six_grid' => 'trainSixGrid',
            'trade_start_time' => 'tradeStartTime', 'trade_end_time' => 'tradeEndTime',
            'ban_90_days' => 'ban90Days', 'common_login_area' => 'commonLoginArea',
            'rank_level' => 'rankLevel', 'rental_duration' => 'rentalDuration',
            'price_ratio' => 'priceRatio', 'rent_days' => 'rentDays',
            'face_is_self' => 'faceIsSelf', 'daily_consume' => 'dailyConsume',
            'liquid_assets' => 'liquidAssets', 'view_count' => 'viewCount',
            'user_id' => 'userId', 'created_at' => 'createdAt', 'updated_at' => 'updatedAt',
        ];
        $out = [];
        foreach ($data as $k => $v) {
            $out[$map[$k] ?? $k] = $v;
        }
        $out['sellerNickname'] = $this->user?->nickname ?? $this->user?->name;
        $out['sellerAvatar'] = $this->user?->avatar;
        if ($showSecret) {
            $out['accountPwd'] = $this->account_pwd;
        }
        return $out;
    }
}
