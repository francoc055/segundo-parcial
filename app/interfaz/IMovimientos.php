<?php
namespace app\interfaz;

interface IMovimientos{

    public static function GetTotalTransaccion($fecha, $tipoCuenta);
    public static function GetTransaccionesDeUnUsuario($email);
    public static function GetTransaccionesEntreFechas($fechaMin, $fechaMax);
    public static function GetTransaccionesPorTipoDeCuentas($tipoCuenta);
    public static function GetTransaccionesPorMoneda($moneda);


}


?>