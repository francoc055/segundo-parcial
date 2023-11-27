<?php
namespace app\models;
use app\db\AccesoDatos;
use app\interfaz\IMovimientos;
use Exception;
use PDO;
class Deposito implements IMovimientos{
    public $fecha;
    public $monto;
    public $idCuenta;

    public function Add()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        try
        {
            $objetoAccesoDato->beginTransaction();
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into depositos (fecha, monto, idCuenta)
                                                        values ('$this->fecha', '$this->monto', '$this->idCuenta');");
            $consulta->execute();

            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE cuentas set cuentas.saldo = cuentas.saldo + '$this->monto' where cuentas.id = '$this->idCuenta';");
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
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT * FROM depositos WHERE depositos.id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, Deposito::class);
        return $consulta->fetch();
    }


    public static function GetTotalTransaccion($fecha, $tipoCuenta){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT SUM(depositos.monto) as total, cuentas.tipoCuenta
                                                    FROM cuentas
                                                    JOIN depositos ON cuentas.id = depositos.idCuenta
                                                    WHERE depositos.fecha = :fecha and cuentas.tipoCuenta = :tipoCuenta and cuentas.estado = 1;");
        $consulta->bindValue(':fecha', $fecha);
        $consulta->bindValue(':tipoCuenta', $tipoCuenta);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;        
    }

    public static function GetTransaccionesDeUnUsuario($email){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT depositos.monto, cuentas.tipoCuenta, depositos.fecha
                                                        FROM cuentas
                                                        JOIN depositos ON cuentas.id = depositos.idCuenta
                                                        where cuentas.email = :email and cuentas.estado = 1;");
        $consulta->bindValue(':email', $email);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista =  $consulta->fetchAll();
        return $lista;        
    }

    public static function GetTransaccionesEntreFechas($fechaMin, $fechaMax){
        $objAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, SUM(depositos.monto) as totalMonto, cuentas.tipoCuenta, depositos.fecha
                                                    FROM cuentas
                                                    JOIN depositos ON cuentas.id = depositos.idCuenta
                                                    where depositos.fecha >= :fechaMin and depositos.fecha <= :fechaMax and cuentas.estado = 1
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
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, depositos.monto as totalMonto, cuentas.tipoCuenta, depositos.fecha
                                                        FROM cuentas
                                                        JOIN depositos ON cuentas.id = depositos.idCuenta
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
        $consulta = $objAccesoDatos->RetornarConsulta("SELECT cuentas.nombre, depositos.monto as totalMonto, cuentas.tipoCuenta, depositos.fecha
                                                        FROM cuentas
                                                        JOIN depositos ON cuentas.id = depositos.idCuenta
                                                        WHERE cuentas.estado = 1 AND cuentas.tipoCuenta LIKE :moneda
                                                        GROUP BY cuentas.nombre;");
        $consulta->bindValue(':moneda', '%' . $moneda);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        $lista = $consulta->fetchAll();
        return $lista;
    }
    
    
}


?>