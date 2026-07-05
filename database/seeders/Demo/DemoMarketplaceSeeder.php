<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\MarketOrderStatus;
use App\Enums\MarketPaymentMethod;
use App\Models\MarketOffer;
use App\Models\MarketOrder;
use App\Models\MarketProduct;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $faker   = \Faker\Factory::create('cs_CZ');
        $admin   = User::where('email', 'admin@demo.cz')->first();
        $members = User::role('member')->get();

        if ($admin === null || $members->isEmpty()) {
            return;
        }

        // Active club offer with several products
        $clubOffer = MarketOffer::factory()->clubOffer()->create([
            'user_id'     => $admin->id,
            'title'       => 'Klubové oblečení 2026',
            'description' => 'Objednávka klubových dresů, mikin a čepic. Platba proběhne stržením z kreditu.',
            'closes_at'   => now()->addDays(21),
        ]);

        $products = [
            ['name' => 'Závodní dres',   'description' => 'Klubový závodní dres s logem, velikosti S–XXL.', 'unit_price' => 890.0,  'qty_available' => null],
            ['name' => 'Klubová mikina', 'description' => 'Mikina s kapucí a výšivkou klubu.',              'unit_price' => 1190.0, 'qty_available' => 30],
            ['name' => 'Čepice / buff',  'description' => 'Sportovní čepice nebo multifunkční šátek.',      'unit_price' => 250.0,  'qty_available' => null],
        ];

        foreach ($products as $product) {
            MarketProduct::factory()->create([
                'market_offer_id' => $clubOffer->id,
                'name'            => $product['name'],
                'description'     => $product['description'],
                'unit_price'      => $product['unit_price'],
                'qty_available'   => $product['qty_available'],
                'payment_method'  => MarketPaymentMethod::CreditCharge,
            ]);
        }

        // Active private (member) offer
        $memberOffer = MarketOffer::factory()->create([
            'user_id'     => $members->random()->id,
            'title'       => 'Prodám čip SIAC',
            'description' => 'Málo používaný SIAC čip, důvodem prodeje je přechod na novější model.',
            'closes_at'   => now()->addDays(10),
        ]);

        MarketProduct::factory()->directPayment()->create([
            'market_offer_id' => $memberOffer->id,
            'name'            => 'SPORTident SIAC',
            'description'     => 'Osobní předání na tréninku, platba převodem.',
            'unit_price'      => 1500.0,
            'qty_available'   => 1,
        ]);

        // Closed offer with history
        $closedOffer = MarketOffer::factory()->clubOffer()->closed()->create([
            'user_id'     => $admin->id,
            'title'       => 'Vánoční objednávka map',
            'description' => 'Hromadná objednávka tréninkových map, již uzavřeno.',
        ]);

        $closedProduct = MarketProduct::factory()->create([
            'market_offer_id' => $closedOffer->id,
            'name'            => 'Sada tréninkových map',
            'description'     => 'Deset tréninkových map okolí.',
            'unit_price'      => 350.0,
        ]);

        // Orders from members in various states
        $clubProducts = $clubOffer->products()->get();

        foreach ($members->random(min(6, $members->count())) as $member) {
            /** @var MarketProduct $product */
            $product = $clubProducts->random();

            MarketOrder::factory()->create([
                'market_product_id' => $product->id,
                'user_id'           => $member->id,
                'qty'               => $faker->numberBetween(1, 2),
                'unit_price'        => $product->unit_price,
                'note'              => $faker->optional(0.4)->randomElement(['Velikost M', 'Velikost L', 'Dámský střih, S']),
                'status'            => $faker->randomElement([MarketOrderStatus::Ordered, MarketOrderStatus::Ordered, MarketOrderStatus::Cancelled]),
            ]);
        }

        foreach ($members->random(min(3, $members->count())) as $member) {
            MarketOrder::factory()->billed()->create([
                'market_product_id' => $closedProduct->id,
                'user_id'           => $member->id,
                'qty'               => 1,
                'unit_price'        => $closedProduct->unit_price,
            ]);
        }
    }
}
