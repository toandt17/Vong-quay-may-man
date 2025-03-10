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
                'name' => 'Vòng Quay May Mắn',
                'description' => 'Quay để nhận những phần quà hấp dẫn',
                'is_active' => true,
            ]);
        }

        // Danh sách giải thưởng
        $prizes = [
            [
                'name' => 'Tiền mặt 500.000đ',
                'image' => null, // Sẽ sử dụng icon thay thế
                'background_color' => '#e74c3c',
                'icon' => 'fa-money-bill',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Giải thưởng tiền mặt trị giá 500.000đ',
            ],
            [
                'name' => 'Voucher 200.000đ',
                'image' => null,
                'background_color' => '#f39c12',
                'icon' => 'fa-ticket-alt',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Voucher mua hàng trị giá 200.000đ',
            ],
            [
                'name' => 'Phân bón cao cấp',
                'image' => null,
                'background_color' => '#2ecc71',
                'icon' => 'fa-seedling',
                'win_rate' => 15.00,
                'quantity' => 30,
                'remaining' => 30,
                'description' => 'Gói phân bón cao cấp cho cây trồng',
            ],
            [
                'name' => 'Thuốc bảo vệ thực vật',
                'image' => null,
                'background_color' => '#3498db',
                'icon' => 'fa-spray-can',
                'win_rate' => 15.00,
                'quantity' => 30,
                'remaining' => 30,
                'description' => 'Thuốc bảo vệ thực vật an toàn và hiệu quả',
            ],
            [
                'name' => 'Hạt giống cao cấp',
                'image' => null,
                'background_color' => '#9b59b6',
                'icon' => 'fa-leaf',
                'win_rate' => 20.00,
                'quantity' => 50,
                'remaining' => 50,
                'description' => 'Gói hạt giống cao cấp cho năng suất cao',
            ],
            [
                'name' => 'Dụng cụ làm vườn',
                'image' => null,
                'background_color' => '#1abc9c',
                'icon' => 'fa-tools',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Bộ dụng cụ làm vườn chất lượng cao',
            ],
            [
                'name' => 'Chúc may mắn lần sau',
                'image' => null,
                'background_color' => '#95a5a6',
                'icon' => 'fa-frown',
                'win_rate' => 0.00,
                'quantity' => 0,
                'remaining' => 0,
                'description' => 'Chúc bạn may mắn lần sau',
            ],
            [
                'name' => 'Áo thun Agrijapan',
                'image' => null,
                'background_color' => '#e67e22',
                'icon' => 'fa-tshirt',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Áo thun cao cấp với logo Agrijapan',
            ],
            [
                'name' => 'Mũ nông dân',
                'image' => null,
                'background_color' => '#f1c40f',
                'icon' => 'fa-hat-cowboy',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Mũ nông dân chống nắng hiệu quả',
            ],
            [
                'name' => 'Găng tay làm vườn',
                'image' => null,
                'background_color' => '#16a085',
                'icon' => 'fa-mitten',
                'win_rate' => 10.00,
                'quantity' => 20,
                'remaining' => 20,
                'description' => 'Găng tay làm vườn chống trầy xước',
            ],
            [
                'name' => 'Thùng phân hữu cơ',
                'image' => null,
                'background_color' => '#27ae60',
                'icon' => 'fa-box',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Thùng phân hữu cơ chất lượng cao',
            ],
            [
                'name' => 'Bình tưới cây',
                'image' => null,
                'background_color' => '#2980b9',
                'icon' => 'fa-fill-drip',
                'win_rate' => 5.00,
                'quantity' => 10,
                'remaining' => 10,
                'description' => 'Bình tưới cây tiện lợi',
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
