<?php
namespace app\models;

use app\db\AccesoDatos;
use PDO;

class Cuenta{
    public $id;
    public $numeroDeCuenta; //emulado - 6 digitos
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $numeroDocumento;
    public $email;
    public $tipoCuenta; //CA$, CAU$S o CC$, CCU$S
    // public $moneda; //$ o U$S
    public $saldo; //0 por defecto

    public function Add()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into cuentas (numeroDeCuenta,nombre,apellido,tipoDocumento,numeroDocumento,email,tipoCuenta)
                                                     values ('$this->numeroDeCuenta','$this->nombre', '$this->apellido', '$this->tipoDocumento', '$this->numeroDocumento', '$this->email', '$this->tipoCuenta')");
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function GetAll()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select * from cuentas where cuentas.estado = 1");
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista = ($consulta->fetchAll());

        return $lista;
    }

    public static function GetById($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM cuentas WHERE cuentas.id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        return $consulta->fetch(\PDO::FETCH_ASSOC);
    }

    public function Update()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.nombre = '$this->nombre',
                                                                            cuentas.apellido = '$this->apellido',
                                                                            cuentas.tipoDocumento = '$this->tipoDocumento',
                                                                            cuentas.numeroDocumento = '$this->numeroDocumento',
                                                                            cuentas.email = '$this->email'
                                                                            where cuentas.id = '$this->id';");
        $consulta->execute();
    }

    public static function Remove($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.estado = 0 where cuentas.id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
    }




    public static function FindCuenta($tipoCuenta, $numeroDeCuenta)
    {
        $lista = self::GetAll();

        foreach($lista as $cuenta)
        { 
            if($cuenta['tipoCuenta'] == $tipoCuenta && $cuenta['numeroDeCuenta'] == $numeroDeCuenta)
            {
                return json_encode(array("moneda" => $cuenta['moneda'], "saldo"=> $cuenta['saldo']));
            }
        }

        return json_encode(array("mensaje" => "cuenta incorrecta"));
    }
    

    public function AsignarNumeroDeCuenta()
    {
        $numero = rand(100000, 999999);
       
        // while(self::VerificarNumeroDeCuenta($numero))
        // {
        //     $numero = rand(100000, 999999);
        // }

        $this->numeroDeCuenta = $numero;
        
    }
}

?>