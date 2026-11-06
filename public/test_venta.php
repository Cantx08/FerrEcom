<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $venta = \App\Models\Venta::create(['cliente_id'=>1,'tipo'=>'online','metodo_pago'=>'tarjeta','estado'=>'pagado','codigo_retiro'=>'ONL-123456','total'=>10]);
    echo "Venta OK\n";
    $factura = \App\Models\Factura::create(['venta_id'=>$venta->id, 'razon_social'=>'Test', 'ruc_cedula'=>'123', 'direccion'=>'Test', 'telefono'=>'123', 'correo'=>'test@test.com']);
    echo "Factura OK\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
