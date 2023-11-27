<?php
namespace app\controller;

use app\models\Ajuste;

class AjusteController{
    
    public function Ajustar($request, $response, $args)
    {
        $body = $request->getParsedBody();

        $ajuste = new Ajuste();
        $ajuste->idTransaccion = $body['idTransaccion'];
        $ajuste->motivo = $body['motivo'];
        $ajuste->tipoAjuste = $body['tipoAjuste'];

        $ajuste->Add();

        return $response->withHeader('Content-Type', 'application/json');  
    }
}

?>