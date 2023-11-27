<?php

use app\controller\AjusteController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

use app\controller\CuentaController;
use app\controller\DepositoController;
use app\controller\RetiroController;

require __DIR__ . '/../vendor/autoload.php';


$app = AppFactory::create();



$app->group('/alta', function (RouteCollectorProxy $group){
    $group->post('/cuenta', CuentaController::class . ':AltaCuenta');
    $group->post('/deposito', DepositoController::class . ':Depositar');
    $group->post('/retiro', RetiroController::class . ':Retirar');
    $group->post('/ajuste', AjusteController::class . ':Ajustar');
});


$app->post('/ConsultarCuenta', CuentaController::class . ':ConsultarCuenta');

$app->put('/modificarCuenta', CuentaController::class . ':ActualizarCuenta');

$app->group('/movimientos', function (RouteCollectorProxy $group){
    $group->get('/total', DepositoController::class . ':TotalTransaccion');
    $group->get('/usuario', DepositoController::class . ':TransaccionesDeUnUsuario');
    $group->get('/entreFechas', DepositoController::class . ':TransaccionesEntreFechas');
    $group->get('/tipoDeCuenta', DepositoController::class . ':TransaccionesPorTipoDeCuentas');
    $group->get('/tipoMoneda', DepositoController::class . ':TransaccionesPorMoneda');
});


$app->run();

?>