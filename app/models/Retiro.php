<?php
namespace app\models;

use app\db\AccesoDatos;
use Exception;
use PDO;

class Retiro{
    public $fecha;
    public $monto;
    public $idCuenta;

    public function Add()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        try
        {
            $objetoAccesoDato->beginTransaction();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into retiros (fecha, monto, idCuenta)
                                                         values ('$this->fecha', '$this->monto', '$this->idCuenta');");
            $consulta->execute();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.saldo = cuentas.saldo - '$this->monto' where cuentas.id = '$this->idCuenta';");
            $consulta->execute();
            
            $objetoAccesoDato->commit();
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
        }catch(Exception $e){
            $objetoAccesoDato->rollBack();
            echo "Error en la transacción: " . $e->getMessage();
        }
       
    }

    public static function GetById($id)
    {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM retiros WHERE retiros.id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, Deposito::class);
        return $consulta->fetch();
    }

    public function VerificarRetiro()
    {
        $cuenta = Cuenta::GetById($this->idCuenta);
        $diferencia = $cuenta['saldo'] - $this->monto;
        if($diferencia >= 0)
        {
            return true;
        }

        return false;
    }
}



?>