<?php

namespace app\controller;

use app\models\Cuenta;
use app\db\AccesoDatos;
class CuentaController{

    public function AltaCuenta($request, $response, $args)
    {
        $body = $request->getParsedBody();

        $cuenta = new Cuenta();

        $cuenta->AsignarNumeroDeCuenta();
        $cuenta->nombre = $body['nombre'];
        $cuenta->apellido = $body['apellido'];
        $cuenta->tipoDocumento = $body['tipoDocumento'];
        $cuenta->numeroDocumento = $body['numeroDocumento'];
        $cuenta->email = $body['email'];
        $cuenta->tipoCuenta = strtoupper($body['tipoCuenta']);



        $ultimoId = $cuenta->Add();
        if($ultimoId > 0)
        {
            $archivo = $_FILES["archivo"]; 
            $archivo["name"] = "$ultimoId-$cuenta->tipoCuenta.jpg";
            $nombre = $archivo["name"];
            $carpeta = "./ImagenesDeCuenta/2023/";
            if (!file_exists($carpeta)) {
                if (!mkdir($carpeta, 0777, true)) {
                    echo json_encode(["Error" => "No se pudo crear la carpeta"]);
                    return;
                }
            }
            $destino = "./ImagenesDeCuenta/2023/".$nombre;
            move_uploaded_file($archivo["tmp_name"],$destino);
            $payload = json_encode(array("mensaje" => "cuenta creada con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Erro al crear la cuenta"));
        }



        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function BajaCuenta($request, $response, $args)
    {
        $param = $request->getQueryParams();
        $id = $param["id"];

        Cuenta::Remove($id);
        
        $payload = json_encode(array("mensaje" => "Baja de la cuenta"));
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function ConsultarCuenta($request, $response, $args)
    {
        $body = $request->getParsedBody();

        $tipoDeCuenta = strtoupper($body['tipoCuenta']);
        $numeroDeCuenta = $body['numeroDeCuenta'];

        $payload = Cuenta::FindCuenta($tipoDeCuenta, $numeroDeCuenta);

        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');  
    }

    public function ActualizarCuenta($request, $response, $args)
    {
        $body = $request->getBody();
        $data = json_decode($body, true);

        $cuenta = new Cuenta();
        $cuenta->id = $data['id'];
        $cuenta->nombre = $data['nombre'];
        $cuenta->apellido = $data['apellido'];
        $cuenta->tipoDocumento = $data['tipoDocumento'];
        $cuenta->numeroDocumento = $data['numeroDocumento'];
        $cuenta->email = $data['email'];

        $cuenta->Update();
        
        return $response->withHeader('Content-Type', 'application/json');  
    }

}

?>