<?php

namespace Database\Seeders;

use App\Models\LuckyWheel;
use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy vòng quay mặc định
        $luckyWheel = LuckyWheel::first();

        if (!$luckyWheel) {
            // Tạo vòng quay mặc định nếu chưa có
            $luckyWheel = LuckyWheel::create([
                'name' => 'Vòng Quay May Mắn AgriJapan',
                'description' => 'Quay để nhận những phần quà hấp dẫn từ AgriJapan',
                'is_active' => true,
            ]);
        }

        // Xóa các giải thưởng cũ của vòng quay này nếu có
        Prize::where('lucky_wheel_id', $luckyWheel->id)->delete();

        // Danh sách giải thưởng mới
        $prizes = [
            [
                'name' => 'Thẻ điện thoại 100k',
                'image' => null,
                'background_color' => '#e74c3c', // Đỏ
                'icon' => 'fa-mobile-alt',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Thẻ nạp điện thoại trị giá 100.000đ',
            ],
            [
                'name' => 'Thẻ điện thoại 50k',
                'image' => null,
                'background_color' => '#f39c12', // Cam
                'icon' => 'fa-phone',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Thẻ nạp điện thoại trị giá 50.000đ',
            ],
            [
                'name' => 'Chúc bạn may mắn lần sau',
                'image' => null,
                'background_color' => '#95a5a6', // Xám
                'icon' => 'fa-heart',
                'win_rate' => 10.00,
                'quantity' => 0,
                'remaining' => 0,
                'description' => 'Cảm ơn quý khách đã tin tưởng sử dụng sản phẩm của AgriJapan',
            ],
            [
                'name' => 'Phiếu mua hàng AgriJapan 200k',
                'image' => null,
                'background_color' => '#2ecc71', // Xanh lá
                'icon' => 'fa-ticket-alt',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Phiếu mua hàng tại AgriJapan trị giá 200.000đ',
            ],
            [
                'name' => 'GP Tạo Hạt Thần Tốc',
                'image' => null,
                'background_color' => '#3498db', // Xanh dương
                'icon' => 'fa-seedling',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Sản phẩm GP Tạo Hạt Thần Tốc cho cây trồng',
            ],
            [
                'name' => 'Nón AgriJapan',
                'image' => null,
                'background_color' => '#9b59b6', // Tím
                'icon' => 'fa-hat-cowboy',
                'win_rate' => 15.00,
                'quantity' => 30,
                'remaining' => 30,
                'description' => 'Nón thời trang mang thương hiệu AgriJapan',
            ],
            [
                'name' => 'Áo thun AgriJapan',
                'image' => null,
                'background_color' => '#1abc9c', // Ngọc lam
                'icon' => 'fa-tshirt',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Áo thun cao cấp với logo AgriJapan',
            ],
            [
                'name' => 'Áo thun AgriJapan',
                'image' => null,
                'background_color' => '#16a085', // Ngọc lam đậm (variant)
                'icon' => 'fa-tshirt',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Áo thun cao cấp với logo AgriJapan',
            ],
            [
                'name' => 'Chúc bạn may mắn lần sau',
                'image' => null,
                'background_color' => '#7f8c8d', // Xám đậm (variant)
                'icon' => 'fa-heart',
                'win_rate' => 10.00,
                'quantity' => 0,
                'remaining' => 0,
                'description' => 'Cảm ơn quý khách đã tin tưởng sử dụng sản phẩm của AgriJapan',
            ],
            [
                'name' => 'Nón AgriJapan',
                'image' => null,
                'background_color' => '#8e44ad', // Tím đậm (variant)
                'icon' => 'fa-hat-cowboy',
                'win_rate' => 15.00,
                'quantity' => 30,
                'remaining' => 30,
                'description' => 'Nón thời trang mang thương hiệu AgriJapan',
            ],
        ];

        // Thêm giải thưởng vào database
        foreach ($prizes as $prizeData) {
            Prize::create(array_merge(
                $prizeData,
                ['lucky_wheel_id' => $luckyWheel->id]
            ));
        }
    }
}
