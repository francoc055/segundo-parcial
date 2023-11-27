<?php
namespace app\models;

use app\db\AccesoDatos;
use Exception;

class Ajuste{
    public $id;
    public $idTransaccion;
    public $motivo;
    public $tipoAjuste;



    public function Add()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        try
        {
            $objetoAccesoDato->beginTransaction();

            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into ajustes (idTransaccion, motivo, tipoAjuste)
                                                            values ('$this->idTransaccion', '$this->motivo', '$this->tipoAjuste');");
            $consulta->execute();

            $this->VerificarAjuste();

            $objetoAccesoDato->commit();
        }
        catch(Exception $e)
        {
            $objetoAccesoDato->rollBack();
            echo "Error en la transacción: " . $e->getMessage();
        }
        
    }


    public function VerificarAjuste()
    {
        if($this->tipoAjuste == 'deposito')
        {
            $transaccion = Deposito::GetById($this->idTransaccion);
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.saldo =  cuentas.saldo + '$transaccion->monto' 
                                                            where cuentas.id = '$transaccion->idCuenta'");
            $consulta->execute();
        }
        else if($this->tipoAjuste == 'retiro')
        {
            $transaccion = Retiro::GetById($this->idTransaccion);
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.saldo =  cuentas.saldo - '$transaccion->monto' 
                                                            where cuentas.id = '$transaccion->idCuenta'");
            $consulta->execute();
        }
        else
        {
            throw new Exception("Error en el tipo de ajuste");
        }
    }

}

?>