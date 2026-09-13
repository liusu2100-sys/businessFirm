<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Banner;
use App\Models\GameAccount;
use App\Models\SiteConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => '管理员',
            'nickname' => '鼠鼠管理员',
            'phone' => '13800000000',
            'email' => 'admin@sssh.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'balance' => 0,
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=admin',
        ]);

        $seller = User::create([
            'name' => '卖家一号',
            'nickname' => '三角洲大佬',
            'phone' => '13800000001',
            'email' => 'seller@sssh.local',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'balance' => 500,
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=seller',
        ]);

        $buyer = User::create([
            'name' => '买家一号',
            'nickname' => '租号玩家',
            'phone' => '13800000002',
            'email' => 'buyer@sssh.local',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'balance' => 2000,
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=buyer',
        ]);

        User::create([
            'name' => '卖家二号',
            'nickname' => '皮肤收藏家',
            'phone' => '13800000003',
            'email' => 'seller2@sssh.local',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'balance' => 300,
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=seller2',
        ]);

        $ranks = ['青铜', '白银', '黄金', '铂金', '钻石', '黑鹰', '巅峰'];
        $areas = ['广东', '浙江', '江苏', '北京', '上海', '四川', '湖北'];
        $logins = ['QQ', '微信', '手机'];
        $melee = ['蝴蝶刀', '短刀', '撬棍', '平底锅'];
        $weapons = ['AWM皮肤', 'M4皮肤', 'AK皮肤', 'Scar皮肤', '无样步枪'];
        $ops = ['干员A', '干员B', '干员C', '干员D'];

        $samples = [
            ['hav' => 850000, 'price' => 88, 'deposit' => 200, 'rank' => '钻石', 'coin' => true],
            ['hav' => 1200000, 'price' => 128, 'deposit' => 300, 'rank' => '黑鹰', 'coin' => true],
            ['hav' => 450000, 'price' => 58, 'deposit' => 150, 'rank' => '铂金', 'coin' => true],
            ['hav' => 2100000, 'price' => 198, 'deposit' => 500, 'rank' => '巅峰', 'coin' => true],
            ['hav' => 320000, 'price' => 48, 'deposit' => 100, 'rank' => '黄金', 'coin' => false],
            ['hav' => 680000, 'price' => 78, 'deposit' => 180, 'rank' => '钻石', 'coin' => true],
            ['hav' => 150000, 'price' => 35, 'deposit' => 80, 'rank' => '白银', 'coin' => false],
            ['hav' => 980000, 'price' => 108, 'deposit' => 250, 'rank' => '黑鹰', 'coin' => true],
            ['hav' => 560000, 'price' => 68, 'deposit' => 160, 'rank' => '铂金', 'coin' => true],
            ['hav' => 1750000, 'price' => 168, 'deposit' => 400, 'rank' => '巅峰', 'coin' => true],
        ];

        foreach ($samples as $i => $s) {
            $owner = $i % 3 === 0 ? User::where('phone', '13800000003')->first() : $seller;
            GameAccount::create([
                'user_id' => $owner->id,
                'account_no' => 'DF' . (10000 + $i),
                'login_type' => $logins[$i % 3],
                'account_pwd' => 'demo_pwd_' . ($i + 1),
                'status' => 1,
                'hav_coin' => $s['hav'],
                'insurance_box' => 50 + $i * 10,
                'stamina_level' => 5 + ($i % 5),
                'weight_level' => 4 + ($i % 4),
                'fh_level' => 3 + ($i % 6),
                'kd_value' => round(1.2 + $i * 0.15, 2),
                'awm_ammo' => 20 + $i * 5,
                'helmet_l6' => $i % 3,
                'armor_l6' => $i % 2,
                'slot9_card' => $i % 4,
                'melee_skins' => array_slice($melee, 0, 1 + $i % 3),
                'weapon_skins' => array_slice($weapons, 0, 1 + $i % 4),
                'operator_skins' => array_slice($ops, 0, 1 + $i % 3),
                'train_six_grid' => $i % 2 === 0,
                'trade_start_time' => '09:00',
                'trade_end_time' => '23:00',
                'ban_90_days' => false,
                'common_login_area' => $areas[$i % count($areas)],
                'rank_level' => $s['rank'],
                'price' => $s['price'],
                'deposit' => $s['deposit'],
                'rental_duration' => 1,
                'remark' => '精品账号，包售后，支持验号。每日可玩时段充足。',
                'price_ratio' => 1.0,
                'rent_days' => 1,
                'face_is_self' => $i % 3 !== 0,
                'daily_consume' => 20 + $i * 5,
                'pic' => 'https://picsum.photos/seed/sssh' . $i . '/400/300',
                'liquid_assets' => round($s['hav'] * 0.01, 2),
                'screenshots' => [
                    'https://picsum.photos/seed/ss' . $i . 'a/800/450',
                    'https://picsum.photos/seed/ss' . $i . 'b/800/450',
                ],
                'view_count' => rand(10, 500),
            ]);
        }

        // one pending
        GameAccount::create([
            'user_id' => $seller->id,
            'account_no' => 'DF20999',
            'login_type' => 'QQ',
            'status' => 0,
            'hav_coin' => 400000,
            'price' => 55,
            'deposit' => 120,
            'rank_level' => '黄金',
            'common_login_area' => '广东',
            'remark' => '待审核样例',
            'pic' => 'https://picsum.photos/seed/pending/400/300',
            'rent_days' => 1,
        ]);

        Banner::create(['title' => '欢迎来到鼠鼠商行', 'image' => 'https://picsum.photos/seed/banner1/1200/360', 'link' => '/products', 'sort' => 10, 'status' => 1]);
        Banner::create(['title' => '新用户专享', 'image' => 'https://picsum.photos/seed/banner2/1200/360', 'link' => '/help', 'sort' => 9, 'status' => 1]);
        Banner::create(['title' => '安全租号保障', 'image' => 'https://picsum.photos/seed/banner3/1200/360', 'link' => '/announcement', 'sort' => 8, 'status' => 1]);

        Announcement::create([
            'title' => '鼠鼠商行上线公告',
            'content' => "<p>欢迎光临<strong>鼠鼠商行</strong>！本平台专注三角洲行动账号租赁中介服务。</p><p>请仔细阅读交易规则，保护好账号安全。</p>",
            'type' => 'notice',
            'is_top' => true,
            'status' => 1,
            'sort' => 100,
        ]);
        Announcement::create([
            'title' => '租赁须知与防骗指南',
            'content' => "<p>1. 下单前请确认账号信息与截图一致。<br>2. 交易中请通过平台沟通，勿私下转账。<br>3. 押金将在订单完成后原路退回余额。</p>",
            'type' => 'notice',
            'is_top' => true,
            'status' => 1,
            'sort' => 90,
        ]);
        Announcement::create([
            'title' => '如何发布账号？',
            'content' => "<p>登录后点击「发布」，填写账号属性、价格与押金，提交后等待审核上架。</p>",
            'type' => 'help',
            'status' => 1,
            'sort' => 80,
        ]);
        Announcement::create([
            'title' => '如何充值与提现？',
            'content' => "<p>充值：个人中心查看充值二维码，转账后联系客服加款。<br>提现：绑定收款账户后提交申请，审核通过后打款。</p>",
            'type' => 'help',
            'status' => 1,
            'sort' => 70,
        ]);
        Announcement::create([
            'title' => '订单状态说明',
            'content' => "<p>待支付 → 交易中 → 已完成。超时未支付将自动取消。支持提前结算与取消退款（交易中）。</p>",
            'type' => 'help',
            'status' => 1,
            'sort' => 60,
        ]);

        $configs = [
            'site_name' => '鼠鼠商行',
            'site_slogan' => '三角洲行动账号租赁平台',
            'commission_rate' => 0.05,
            'withdraw_fee_rate' => 0.02,
            'min_withdraw' => 10,
            'order_pay_timeout_minutes' => 30,
            'cs_contact' => '客服微信：sssh_cs',
            'recharge_qrcode' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=sssh-recharge-demo',
            'recharge_tips' => '请扫码转账后联系客服充值，备注您的手机号',
            'recharge_amounts' => [50, 100, 200, 500, 1000],
            'login_types' => ['QQ', '微信', '手机'],
            'rank_levels' => $ranks,
            'melee_skin_options' => $melee,
            'weapon_skin_options' => $weapons,
            'operator_skin_options' => $ops,
            'filter_fields' => [
                ['key' => 'loginType', 'label' => '登录方式', 'type' => 'select'],
                ['key' => 'rankLevel', 'label' => '段位', 'type' => 'select'],
                ['key' => 'minPrice', 'label' => '最低价', 'type' => 'number'],
                ['key' => 'maxPrice', 'label' => '最高价', 'type' => 'number'],
                ['key' => 'minHavCoin', 'label' => '最低哈夫币', 'type' => 'number'],
                ['key' => 'trainSixGrid', 'label' => '训练六格', 'type' => 'boolean'],
                ['key' => 'faceIsSelf', 'label' => '本人脸', 'type' => 'boolean'],
                ['key' => 'commonLoginArea', 'label' => '常登录地', 'type' => 'text'],
            ],
            'home_notice' => '平台担保交易，资金安全有保障。如遇问题请联系在线客服。',
            'pricing_formula_tip' => '租金建议 = 哈夫币估值 × 系数 + 皮肤溢价',
        ];
        foreach ($configs as $k => $v) {
            SiteConfig::setValue($k, $v);
        }
    }
}
