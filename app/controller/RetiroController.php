<?php
namespace app\controller;

use app\models\Retiro;

class RetiroController{
    
    public function Retirar($request, $response, $args)
    {
        $body = $request->getParsedBody();

        $retiro = new Retiro();
        $retiro->fecha = date("Y-m-d H:i:s");
        $retiro->monto = $body['monto'];
        $retiro->idCuenta = $body['idCuenta'];

        if($retiro->VerificarRetiro())
        {
            $retiro->Add();

            $payload = json_encode(array("mensaje" => "Retiro creado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Error. saldo insuficiente"));
        }
        
        
        
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

        $lista = Retiro::GetTotalTransaccion($fecha, $tipoCuenta);

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

        $lista = Retiro::GetTransaccionesDeUnUsuario($email);
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

        $lista = Retiro::GetTransaccionesEntreFechas($fechaMin, $fechaMax);
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

        $lista = Retiro::GetTransaccionesPorTipoDeCuentas($tipoCuenta);
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

        $lista = Retiro::GetTransaccionesPorMoneda($moneda);

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

    public function RetirosDepositosDeUnUsuario($request, $response, $args)
    {
        $param = $request->getQueryParams();

        $email = $param['email'];

        $lista = Retiro::GetRetirosDepositosDeUnUsuario($email);

        $payload = json_encode($lista);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>