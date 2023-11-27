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
        $consulta->setFetchMode(PDO::FETCH_CLASS, Retiro::class);
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

    public static function GetTotalTransaccion($fecha, $tipoCuenta){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT SUM(retiros.monto) as total, cuentas.tipoCuenta
                                                    FROM cuentas
                                                    JOIN retiros ON cuentas.id = retiros.idCuenta
                                                    WHERE retiros.fecha = :fecha and cuentas.tipoCuenta = :tipoCuenta and cuentas.estado = 1;");
        $consulta->bindValue(':fecha', $fecha);
        $consulta->bindValue(':tipoCuenta', $tipoCuenta);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;        
    }

    public static function GetTransaccionesDeUnUsuario($email){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT retiros.monto, cuentas.tipoCuenta, retiros.fecha
                                                        FROM cuentas
                                                        JOIN retiros ON cuentas.id = retiros.idCuenta
                                                        where cuentas.email = :email and cuentas.estado = 1;");
        $consulta->bindValue(':email', $email);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;        
    }

    public static function GetTransaccionesEntreFechas($fechaMin, $fechaMax){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, SUM(retiros.monto) as totalMonto, cuentas.tipoCuenta, retiros.fecha
                                                    FROM cuentas
                                                    JOIN retiros ON cuentas.id = retiros.idCuenta
                                                    where retiros.fecha >= :fechaMin and retiros.fecha <= :fechaMax and cuentas.estado = 1
                                                    GROUP BY cuentas.nombre;");
        $consulta->bindValue(':fechaMin', $fechaMin);
        $consulta->bindValue(':fechaMax', $fechaMax);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;   
    }
    public static function GetTransaccionesPorTipoDeCuentas($tipoCuenta){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, retiros.monto as totalMonto, cuentas.tipoCuenta, retiros.fecha
                                                        FROM cuentas
                                                        JOIN retiros ON cuentas.id = retiros.idCuenta
                                                        where cuentas.estado = 1 and cuentas.tipoCuenta LIKE :tipoCuenta
                                                        GROUP BY cuentas.nombre;");
        $consulta->bindValue(':tipoCuenta', $tipoCuenta . '%');
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;
    }
    
    public static function GetTransaccionesPorMoneda($moneda) {
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, retiros.monto as totalMonto, cuentas.tipoCuenta, retiros.fecha
                                                        FROM cuentas
                                                        JOIN retiros ON cuentas.id = retiros.idCuenta
                                                        WHERE cuentas.estado = 1 AND cuentas.tipoCuenta LIKE :moneda
                                                        GROUP BY cuentas.nombre;");
        $consulta->bindValue(':moneda', '%' . $moneda);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista = $consulta->fetchAll();
        return $lista;
    }


    public static function GetRetirosDepositosDeUnUsuario($email){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT retiros.monto, cuentas.tipoCuenta, cuentas.nombre
                                                    FROM retiros
                                                    JOIN cuentas ON retiros.idCuenta = cuentas.id
                                                    WHERE cuentas.email = :email and cuentas.estado = 1;");
        $consulta->bindValue(':email', $email);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();

        $listaRetiros = $consulta->fetchAll();

        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT depositos.monto, cuentas.tipoCuenta, cuentas.nombre
                                                    FROM depositos
                                                    JOIN cuentas ON depositos.idCuenta = cuentas.id
                                                    WHERE cuentas.email = :email and cuentas.estado = 1;");
        $consulta->bindValue(':email', $email);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();

        $listaDepositos= $consulta->fetchAll();

        $transacciones = [$listaRetiros, $listaDepositos];
        return $transacciones;
    }
}





?>