<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Catálogo de productos típicos de tienda de barrio colombiana,
     * agrupados por categoría para que el seeder asigne category_id correcto.
     */
    private array $catalog = [
        'Bebidas' => [
            ['Coca-Cola 350ml', 2500, 1700],
            ['Coca-Cola 1.5L', 6500, 4800],
            ['Pepsi 400ml', 2200, 1500],
            ['Agua Cristal 600ml', 1800, 1100],
            ['Agua Cristal 1L', 2500, 1600],
            ['Jugo Hit Mora 200ml', 1500, 950],
            ['Jugo Hit Mango 200ml', 1500, 950],
            ['Pony Malta 354ml', 2800, 1900],
            ['Gatorade Azul 500ml', 4500, 3200],
            ['Cerveza Águila 330ml', 3200, 2300],
            ['Cerveza Poker 330ml', 3200, 2300],
            ['Cerveza Club Colombia 330ml', 3800, 2700],
            ['Té Hatsu Limón 400ml', 3000, 2100],
            ['Café Águila Roja 500g', 12000, 9000],
            ['Avena Alpina 300ml', 2800, 1900],
        ],
        'Snacks' => [
            ['Papas Margarita 25g', 1800, 1200],
            ['Papas Margarita 130g', 5500, 4000],
            ['Detodito 22g', 1700, 1100],
            ['Doritos Nacho 52g', 3500, 2500],
            ['Platanitos Margarita 25g', 1800, 1200],
            ['Chocoramo', 2200, 1500],
            ['Galletas Festival', 2000, 1300],
            ['Galletas Saltín Noel', 2500, 1700],
            ['Chicles Trident', 1500, 950],
            ['Bon Bon Bum', 300, 180],
            ['Mentas Glacial', 1200, 750],
            ['Maní Moto x10', 1000, 600],
            ['Choco Break', 2300, 1600],
            ['Galletas Tosh', 3200, 2300],
            ['Ponqué Ramo', 3500, 2500],
        ],
        'Aseo' => [
            ['Jabón Rey x3', 4500, 3200],
            ['Jabón Protex', 3200, 2200],
            ['Shampoo Savital 350ml', 8500, 6200],
            ['Crema Dental Colgate 75ml', 4200, 3000],
            ['Papel Higiénico Familia x4', 6500, 4800],
            ['Papel Higiénico Familia x12', 18000, 13500],
            ['Detergente Fab 500g', 5500, 4000],
            ['Detergente Ariel 1kg', 11000, 8200],
            ['Suavitel 800ml', 7500, 5500],
            ['Esponjilla Brillo x3', 2500, 1700],
            ['Escoba plástica', 9500, 6800],
            ['Bolsas de basura x10', 3500, 2400],
            ['Desinfectante Fabuloso 1L', 6800, 4900],
            ['Toallas Higiénicas Nosotras', 5200, 3700],
            ['Pañales Huggies x10', 22000, 17000],
        ],
        'Lácteos' => [
            ['Leche Alpina 1L', 4200, 3300],
            ['Leche Algarra 1L', 4000, 3100],
            ['Yogurt Alpina 200ml', 2800, 2000],
            ['Yogurt Alquería 1L', 8500, 6500],
            ['Kumis Alpina 1L', 6500, 4900],
            ['Queso Campesino 250g', 7500, 5800],
            ['Mantequilla Alpina 250g', 6800, 5100],
            ['Arequipe Alpina 250g', 5500, 4200],
            ['Avena Liquida Alpina 1L', 5000, 3800],
        ],
        'Abarrotes' => [
            ['Arroz Diana 500g', 2800, 2100],
            ['Arroz Diana 1kg', 5200, 4000],
            ['Aceite Premier 1L', 9500, 7500],
            ['Azúcar Manuelita 1kg', 3800, 2900],
            ['Sal Refisal 500g', 1500, 1000],
            ['Panela Cuadrada x2', 3500, 2600],
            ['Lenteja x500g', 4200, 3200],
            ['Fríjol Cargamanto x500g', 5500, 4200],
            ['Pasta La Muñeca 500g', 2500, 1800],
            ['Chocolate Corona x250g', 6500, 4900],
            ['Huevos AA x30', 14000, 11500],
            ['Huevos AA x12', 6200, 5000],
            ['Harina Pan 1kg', 3800, 2900],
            ['Maizena 200g', 3200, 2400],
            ['Salsa de Tomate Fruco 400g', 5500, 4100],
            ['Mayonesa Fruco 400g', 6800, 5100],
            ['Atún Van Camps', 5200, 3900],
            ['Sardinas Cunit', 4800, 3600],
            ['Caldo Maggi x6', 3500, 2500],
            ['Vinagre Doña Gusta 500ml', 2200, 1500],
        ],
        'Panadería' => [
            ['Pan Tajado Bimbo', 6500, 4900],
            ['Pan Blandito x6', 4500, 3300],
            ['Mogolla x4', 3800, 2700],
            ['Pan Francés x4', 3000, 2100],
            ['Almojábana x4', 4200, 3000],
            ['Pan de Queso x6', 5500, 4000],
            ['Tostadas Bimbo', 5800, 4300],
        ],
        'Aseo personal' => [
            ['Desodorante Rexona', 7500, 5600],
            ['Crema Nivea 100ml', 9500, 7200],
            ['Máquina de Afeitar Bic x2', 4500, 3300],
            ['Toallas Faciales Familia', 3500, 2500],
            ['Cepillo de dientes', 3800, 2600],
            ['Enjuague Bucal Listerine 250ml', 11000, 8500],
        ],
        'Cigarrería y varios' => [
            ['Cigarrillos Marlboro', 9500, 7800],
            ['Fósforos x1', 800, 500],
            ['Encendedor Bic', 3500, 2200],
            ['Pilas Varta AA x2', 4500, 3200],
            ['Minutos a todo operador', 1000, 700],
            ['Recarga Claro 2000', 2000, 1800],
            ['Recarga Movistar 5000', 5000, 4500],
            ['Loto / Chance', 2000, 0],
        ],
    ];

    private array $colors = ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#14b8a6', '#f97316'];

    private array $firstNames = ['Carlos', 'María', 'José', 'Luisa', 'Andrés', 'Diana', 'Jorge', 'Camila', 'Pedro', 'Sandra', 'Luis', 'Paola', 'Miguel', 'Laura', 'Fernando', 'Claudia', 'Ricardo', 'Adriana', 'Daniel', 'Patricia'];
    private array $lastNames  = ['Gómez', 'Rodríguez', 'Martínez', 'López', 'García', 'Pérez', 'Hernández', 'Díaz', 'Torres', 'Ramírez', 'Castro', 'Rojas', 'Morales', 'Suárez', 'Vargas'];
    private array $storeWords = ['La Esquina', 'El Progreso', 'San José', 'La Estrella', 'El Vecino', 'Doña Flor', 'La Fortuna', 'El Ahorro', 'Santa Rita', 'La Económica', 'El Triunfo', 'Don Pedro', 'La 15', 'El Paisa', 'La Bendición', 'El Surtidor', 'La Gran Vía', 'Mi Tienda', 'El Recreo', 'La Bonita'];
    private array $neighborhoods = ['Las Flores', 'El Carmen', 'San Antonio', 'La Esperanza', 'Bella Vista', 'El Recreo', 'Los Pinos', 'San Pablo', 'La Floresta', 'El Rincón'];

    public function run(): void
    {
        $this->command->info('Limpiando datos de prueba previos (owner emails demo*)...');

        DB::transaction(function () {
            for ($ownerIndex = 1; $ownerIndex <= 10; $ownerIndex++) {
                $this->seedOwner($ownerIndex);
            }
        });

        $this->command->info('✅ Listo: 10 dueños, 20 tiendas, ~2000 productos, ~2000 ventas.');
        $this->command->info('Login de prueba: demo1@mitiendadigital.app ... demo10@mitiendadigital.app');
        $this->command->info('Password para todos: 123456');
    }

    private function seedOwner(int $i): void
    {
        $firstName = $this->firstNames[($i - 1) % count($this->firstNames)];
        $lastName  = $this->lastNames[($i - 1) % count($this->lastNames)];

        $ownerId = DB::table('owners')->insertGetId([
            'name'       => "{$firstName} {$lastName}",
            'email'      => "demo{$i}@mitiendadigital.app",
            'password'   => Hash::make('123456'),
            'phone'      => '300' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
            'active'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Dueño {$i}/10: {$firstName} {$lastName} ({$ownerId})");

        // 2 tiendas por dueño
        for ($s = 1; $s <= 2; $s++) {
            $this->seedStore($ownerId, $i, $s);
        }
    }

    private function seedStore(int $ownerId, int $ownerIndex, int $storeSlot): void
    {
        $storeName  = $this->storeWords[array_rand($this->storeWords)] . ' ' . ($storeSlot === 1 ? '' : 'II');
        $storeName  = trim($storeName);
        $neighborhood = $this->neighborhoods[array_rand($this->neighborhoods)];

        $storeId = DB::table('stores')->insertGetId([
            'owner_id'   => $ownerId,
            'name'       => "Tienda {$storeName}",
            'address'    => "Calle " . random_int(1, 99) . " #" . random_int(1, 99) . "-" . random_int(1, 99) . ", {$neighborhood}",
            'phone'      => '300' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
            'active'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Suscripción activa de 6 meses, ya pagada, para que no la bloquee CheckSubscription
        DB::table('subscriptions')->insert([
            'owner_id'   => $ownerId,
            'store_id'   => $storeId,
            'plan'       => '6_months',
            'price'      => 567000,
            'start_date' => Carbon::now()->subDays(15),
            'end_date'   => Carbon::now()->addMonths(6),
            'active'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Categorías (una por grupo del catálogo)
        $categoryIds = [];
        foreach (array_keys($this->catalog) as $catName) {
            $categoryIds[$catName] = DB::table('categories')->insertGetId([
                'store_id'   => $storeId,
                'name'       => $catName,
                'color'      => $this->colors[array_rand($this->colors)],
                'active'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 100 productos repartidos entre las categorías del catálogo
        $productIds = $this->seedProducts($storeId, $categoryIds);

        // 25 clientes (suficientes para repartir 100 ventas, varias por cliente + algunas sin cliente)
        $customerIds = $this->seedCustomers($storeId);

        // 100 ventas con sus items, descuento real de stock e inventory_logs
        $this->seedSales($storeId, $productIds, $customerIds);

        $this->command->info("  └─ Tienda creada: \"Tienda {$storeName}\" (ID {$storeId}) — 100 productos, 100 ventas");
    }

    private function seedProducts(int $storeId, array $categoryIds): array
    {
        // Aplanar el catálogo en [categoryName, name, price, cost]
        $flat = [];
        foreach ($this->catalog as $catName => $items) {
            foreach ($items as [$name, $price, $cost]) {
                $flat[] = [$catName, $name, $price, $cost];
            }
        }
        // El catálogo tiene ~106 ítems, lo recortamos/rotamos a exactamente 100
        while (count($flat) < 100) {
            $flat[] = $flat[array_rand($flat)];
        }
        shuffle($flat);
        $flat = array_slice($flat, 0, 100);

        $now = now();
        $rows = [];
        $usedBarcodes = [];

        foreach ($flat as $idx => [$catName, $name, $price, $cost]) {
            // Variar un poco el precio/costo por tienda para que no sean clones exactos
            $variance = random_int(-5, 5) / 100;
            $finalPrice = max(500, round($price * (1 + $variance), -2));
            $finalCost  = max(300, round($cost * (1 + $variance), -2));

            $stock    = random_int(0, 80);
            $minStock = random_int(3, 10);

            // Barcode único por tienda (no global, pero evitamos choques dentro del insert)
            do {
                $barcode = '750' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
            } while (isset($usedBarcodes[$barcode]));
            $usedBarcodes[$barcode] = true;

            $rows[] = [
                'store_id'    => $storeId,
                'category_id' => $categoryIds[$catName],
                'name'        => $name,
                'barcode'     => $barcode,
                'price'       => $finalPrice,
                'cost'        => $finalCost,
                'stock'       => $stock,
                'min_stock'   => $minStock,
                'active'      => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        DB::table('products')->insert($rows);

        // Recuperar IDs en el mismo orden de inserción (por store_id, recién creados)
        return DB::table('products')
            ->where('store_id', $storeId)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();
    }

    private function seedCustomers(int $storeId): array
    {
        $now = now();
        $rows = [];

        for ($c = 1; $c <= 25; $c++) {
            $first = $this->firstNames[array_rand($this->firstNames)];
            $last  = $this->lastNames[array_rand($this->lastNames)];

            $rows[] = [
                'store_id'   => $storeId,
                'name'       => "{$first} {$last}",
                'phone'      => '3' . random_int(0, 2) . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address'    => 'Cra ' . random_int(1, 80) . ' #' . random_int(1, 99) . '-' . random_int(1, 99),
                'total_debt' => 0, // se actualiza durante seedSales si quedan ventas fiadas
                'active'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('customers')->insert($rows);

        return DB::table('customers')
            ->where('store_id', $storeId)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();
    }

    private function seedSales(int $storeId, array $productIds, array $customerIds): void
    {
        $now = now();
        $debtByCustomer = [];

        for ($s = 1; $s <= 100; $s++) {
            // Fecha aleatoria en los últimos 60 días, para que Reportes tenga datos repartidos
            $createdAt = Carbon::now()->subDays(random_int(0, 60))->subMinutes(random_int(0, 1439));

            // 70% contado, 30% fiado (fiado requiere cliente)
            $isFiado = random_int(1, 100) <= 30;
            $customerId = null;

            if ($isFiado || random_int(1, 100) <= 50) {
                // incluso ventas de contado a veces tienen cliente asociado
                $customerId = $customerIds[array_rand($customerIds)];
            }
            if ($isFiado && !$customerId) {
                $customerId = $customerIds[array_rand($customerIds)];
            }

            $type = $isFiado ? 'fiado' : 'contado';

            // 2 a 6 productos distintos por venta
            $itemCount = random_int(2, 6);
            $chosenProducts = (array) array_rand(array_flip($productIds), min($itemCount, count($productIds)));
            if (!is_array($chosenProducts)) {
                $chosenProducts = [$chosenProducts];
            }

            $total = 0;
            $itemsToInsert = [];
            $stockUpdates = [];

            foreach ($chosenProducts as $productId) {
                $product = DB::table('products')->where('id', $productId)->first();
                if (!$product) continue;

                $qty = random_int(1, 5);
                // Evitar vender más de lo que hay; si no hay stock, vender 1 igual
                // (estos son datos demo, no afecta producción real)
                $qtyToSell = $product->stock > 0 ? min($qty, max(1, $product->stock)) : 1;

                $subtotal = $product->price * $qtyToSell;
                $total += $subtotal;

                $itemsToInsert[] = [
                    'product_id' => $productId,
                    'quantity'   => $qtyToSell,
                    'unit_price' => $product->price,
                    'subtotal'   => $subtotal,
                ];

                $stockUpdates[] = [
                    'product_id'    => $productId,
                    'stock_before'  => $product->stock,
                    'stock_after'   => max(0, $product->stock - $qtyToSell),
                    'qty'           => $qtyToSell,
                ];
            }

            if (empty($itemsToInsert)) {
                continue;
            }

            // Definir pago según tipo
            if ($type === 'contado') {
                $paid = $total;
            } else {
                // fiado: a veces no paga nada, a veces abona algo
                $payOptions = [0, 0, 0.3, 0.5, 0.7];
                $payRatio   = $payOptions[array_rand($payOptions)];
                $paid       = round($total * $payRatio, -2);
            }

            $debt   = max(0, $total - $paid);
            $status = $debt <= 0 ? 'pagada' : ($paid > 0 ? 'parcial' : 'pendiente');

            $saleId = DB::table('sales')->insertGetId([
                'store_id'      => $storeId,
                'customer_id'   => $customerId,
                'store_user_id' => null,
                'type'          => $type,
                'total'         => $total,
                'paid'          => $paid,
                'debt'          => $debt,
                'status'        => $status,
                'notes'         => null,
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            // sale_items
            $saleItemRows = [];
            foreach ($itemsToInsert as $item) {
                $saleItemRows[] = [
                    'sale_id'    => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal'   => $item['unit_price'] * $item['quantity'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
            DB::table('sale_items')->insert($saleItemRows);

            // Descontar stock real + inventory_logs
            $logRows = [];
            foreach ($stockUpdates as $upd) {
                DB::table('products')->where('id', $upd['product_id'])->update(['stock' => $upd['stock_after']]);

                $logRows[] = [
                    'store_id'      => $storeId,
                    'product_id'    => $upd['product_id'],
                    'store_user_id' => null,
                    'type'          => 'venta',
                    'quantity'      => $upd['qty'],
                    'stock_before'  => $upd['stock_before'],
                    'stock_after'   => $upd['stock_after'],
                    'note'          => "Venta #{$saleId}",
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ];
            }
            DB::table('inventory_logs')->insert($logRows);

            // Acumular deuda por cliente para actualizar customers.total_debt al final
            if ($customerId && $debt > 0) {
                $debtByCustomer[$customerId] = ($debtByCustomer[$customerId] ?? 0) + $debt;
            }
        }

        // Aplicar deuda acumulada a cada cliente de esta tienda
        foreach ($debtByCustomer as $customerId => $totalDebt) {
            DB::table('customers')->where('id', $customerId)->update(['total_debt' => $totalDebt]);
        }
    }
}