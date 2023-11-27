<?php
namespace app\controller;

use app\models\Cuenta;
use app\models\Deposito;

class DepositoController{

    public function Depositar($request, $response, $args)
    {

        
        $body = $request->getParsedBody();

        $deposito = new Deposito();
        $deposito->fecha = date("Y-m-d H:i:s");
        $deposito->monto = $body['monto'];
        $deposito->idCuenta = $body['idCuenta'];

        $deposito->Add();

        $payload = json_encode(array("mensaje" => "Deposito creado con exito"));
        
        
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');  


    }

    public function TotalTransaccion($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $fecha = "";
        if(isset($param['fecha']))
        {
            $fecha = $param['fecha'];
        }
        else
        {
            $fecha = date('y-m-d');
            $fecha = date('y-m-d', strtotime('-1 day', strtotime($fecha)));
        }
        $tipoCuenta = $param['tipoCuenta'];

        $lista = Deposito::GetTotalTransaccion($fecha, $tipoCuenta);

        if($lista[0]['total'] == null)
        {
            $payload = json_encode(array('mensaje' => 'no hay datos para mostrar'));
        }
        else
        {
            $payload = json_encode($lista);
        }


        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function TransaccionesDeUnUsuario($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $email = $param['email'];

        $lista = Deposito::GetTransaccionesDeUnUsuario($email);
        if(count($lista) > 0)
        {
            $payload = json_encode($lista);
        }
        else
        {
            $payload = json_encode(array('mensaje' => 'no hay datos para mostrar'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function TransaccionesEntreFechas($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $fechaMin = $param['fechaMin'];
        $fechaMax = $param['fechaMax'];

        $lista = Deposito::GetTransaccionesEntreFechas($fechaMin, $fechaMax);
        if(count($lista) > 0)
        {
            $payload = json_encode($lista);
        }
        else
        {
            $payload = json_encode(array('mensaje' => 'no hay datos para mostrar'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function TransaccionesPorTipoDeCuentas($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $tipoCuenta = $param['tipoCuenta'];

        $lista = Deposito::GetTransaccionesPorTipoDeCuentas($tipoCuenta);
        if(count($lista) > 0)
        {
            $payload = json_encode($lista);
        }
        else
        {
            $payload = json_encode(array('mensaje' => 'no hay datos para mostrar'));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function TransaccionesPorMoneda($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $moneda = $param['moneda'];

        $lista = Deposito::GetTransaccionesPorMoneda($moneda);

        if(count($lista) > 0)
        {
            $payload = json_encode($lista);
        }
        else
        {
            $payload = json_encode(array('mensaje' => 'no hay datos para mostrar'));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }
    
}
?>