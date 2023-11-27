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
}

?>