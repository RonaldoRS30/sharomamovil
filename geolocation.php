<?php
session_start();
require_once("config/db.php");
require_once("config/conexion.php");

    if(isset($_GET['lat']) && isset($_GET['lng'])){
        $id_vendedor = $_SESSION['DIREP_Codigo'];
        //Comprobar si es un vendedor
        $sqlDirectivo = "SELECT * FROM cji_directivo WHERE DIREP_Codigo = $id_vendedor";
        $queryDirectivo = mysqli_query($con, $sqlDirectivo);
        while($di=mysqli_fetch_array($queryDirectivo)){
            $cargo = $di['CARGP_Codigo'];
        }

        if($cargo == 2){
            //Recuperar fecha actual
            date_default_timezone_set('America/Lima');
            $hoy=date("Y-m-d h:i:s");
            $ActualFecha = new DateTime($hoy);
            $latitud = $_GET['lat'];
            $longitud = $_GET['lng'];
            $exist = false;

            //Recuperar último tick ingresado
            $sql="SELECT * from cji_rastreo WHERE DIREP_Codigo = $id_vendedor  AND RS_LogFlag = 0 ORDER BY RS_Codigo DESC LIMIT 1 ";
            $query=mysqli_query($con,$sql);

            while($rw=mysqli_fetch_array($query)){
                $codRas = $rw['RS_Codigo'];
                $ubi1 = $rw['RS_ubi1'];
                $ubi2 = $rw['RS_ubi2'];
                $ubi3 = $rw['RS_ubi3'];
                $ubi4 = $rw['RS_ubi4'];
                $Fecha1 =$rw['RS_FechaRegistro1'];
                $Fecha2 =$rw['RS_FechaRegistro2'];
                $Fecha3 =$rw['RS_FechaRegistro3'];
                $Fecha4 =$rw['RS_FechaRegistro4'];
                $exist = true;
            }

            if($exist == true){
                if($Fecha4 != null){
                    $LastFecha = new DateTime($Fecha4);
                    $posi = 4;
                }else if($Fecha3 != null){
                    $LastFecha = new DateTime($Fecha3);
                    $posi = 3;
                }else if($Fecha2 != null){
                    $LastFecha = new DateTime($Fecha2);
                    $posi = 2;
                }else{
                    $LastFecha = new DateTime($Fecha1);
                    $posi = 1;
                }
                
                
                $rest = $ActualFecha->diff($LastFecha);
                $mes = $rest->format('%m');
                $dia = $rest->format('%d');
                $hora = $rest->format('%h');
                $minuto = $rest->format('%i');
                
                if($dia == 0 && $mes == 0){
                    if($minuto >= 15){
                        switch($posi){
                            case 4:
                                $sqlRastreo = "INSERT INTO cji_rastreo(DIREP_Codigo, RS_ubi1, RS_FechaRegistro1, RS_LogFlag)
                                                    VALUES($id_vendedor, '$latitud,$longitud', '$hoy', '0')";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;
                            case 1:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi2 = '$latitud,$longitud', RS_FechaRegistro2 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break; 
                            case 2:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi3 = '$latitud,$longitud', RS_FechaRegistro3 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;
                            case 3:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi4 = '$latitud,$longitud', RS_FechaRegistro4 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;   
                        }
                        if(!$queryRastreo){
                            var_dump("error en el registro");
                        }else{
                            var_dump("registrado exitosamente");
                        }
                    }
                }else{
                    $sqlRastreo = "INSERT INTO cji_rastreo(DIREP_Codigo, RS_ubi1, RS_FechaRegistro1, RS_LogFlag)
                                VALUES($id_vendedor, '$latitud,$longitud', '$hoy', '0')";
                    $queryRastreo = mysqli_query($con, $sqlRastreo);
    
                    if(!$queryRastreo){
                        var_dump("error en el registro");
                    }else{
                        var_dump("registrado exitosamente");
                    }
                }
            }else{
                $sqlRastreo = "INSERT INTO cji_rastreo(DIREP_Codigo, RS_ubi1, RS_FechaRegistro1, RS_LogFlag)
                                VALUES($id_vendedor, '$latitud,$longitud', '$hoy', '0')";
                $queryRastreo = mysqli_query($con, $sqlRastreo);
                if(!$queryRastreo){
                    var_dump("error en el registro");
                }else{
                    var_dump("registrado exitosamente");
                }
            }
        }
    }

    if(isset($_GET['log'])){
        $exist = false;
        $sql="SELECT * from cji_rastreo WHERE DIREP_Codigo = $id_vendedor AND RS_LogFlag = 1 ORDER BY RS_Codigo DESC LIMIT 1 ";
            $query=mysqli_query($con,$sql);

            while($rw=mysqli_fetch_array($query)){
                $codRas = $rw['RS_Codigo'];
                $Fecha1 =$rw['RS_FechaRegistro1'];
                $Fecha2 =$rw['RS_FechaRegistro2'];
                $Fecha3 =$rw['RS_FechaRegistro3'];
                $Fecha4 =$rw['RS_FechaRegistro4'];
                $exist = true;
            }

            if($exist == true){
                if($Fecha4 != null){
                    $LastFecha = new DateTime($Fecha4);
                    $posi = 4;
                }else if($Fecha3 != null){
                    $LastFecha = new DateTime($Fecha3);
                    $posi = 3;
                }else if($Fecha2 != null){
                    $LastFecha = new DateTime($Fecha2);
                    $posi = 2;
                }else{
                    $LastFecha = new DateTime($Fecha1);
                    $posi = 1;
                }
                
                
                $rest = $ActualFecha->diff($LastFecha);
                $mes = $rest->format('%m');
                $dia = $rest->format('%d');
                $hora = $rest->format('%h');
                $minuto = $rest->format('%i');
                if($dia == 0 && $mes == 0){
                    if($minuto >= 15){
                        switch($posi){
                            case 4:
                                $sqlRastreo = "INSERT INTO cji_rastreo(DIREP_Codigo, RS_ubi1, RS_FechaRegistro1, RS_LogFlag)
                                                    VALUES($id_vendedor, '$latitud,$longitud', '$hoy', '0')";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;
                            case 1:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi2 = '$latitud,$longitud', RS_FechaRegistro2 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break; 
                            case 2:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi3 = '$latitud,$longitud', RS_FechaRegistro3 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;
                            case 3:
                                $sqlRastreo = "UPDATE cji_rastreo SET RS_ubi4 = '$latitud,$longitud', RS_FechaRegistro4 = '$hoy' WHERE RS_Codigo = $codRas";
                                $queryRastreo = mysqli_query($con, $sqlRastreo);
                            break;   
                        }
                        if(!$queryRastreo){
                            var_dump("error en el registro");
                        }else{
                            var_dump("registrado exitosamente");
                        }
                    }
                }
        }
    }
?>