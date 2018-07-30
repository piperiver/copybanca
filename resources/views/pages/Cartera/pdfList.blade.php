<!DOCTYPE html>
<html>
      <head>
        <meta charset="utf-8">
        <title></title>
        <style>
            html{
                margin: 10px 40px;
            }
            .text-center{
                text-align: center;
            }
            .title{
                font-size: 18px;
                margin: 0;
            }
            .col-5{
                display: inline-block;
                width: 49%;
            }
            .col-3{
                display: inline-block;
                width: 33%;
            }
            *{
                font-family: "Open Sans",sans-serif;
                font-size: 10px;
            }
            .uppercase{
                text-transform: uppercase;
            }
            label{
                font-weight: bold;
            }
            table{
                width: 100%;
                border-spacing: 0;
            }
            .border{
                border: 1px solid #4266b2;
            }
            .border-bottom{
                border-bottom: 1px solid #4266b2;
            }
            table tr td{
                padding: 2px;
            }
            .caption{
                background: #4266b2 !important;
                color: #fff;
                font-weight: bold;
                padding: 0;
            }
            .margin-top-20{
                margin-top: 8px;
            }
            .plan th, .plan td{
                font-size: 10px;
            }
            .columna{
                width: 49%;
                display: inline-block;
            }
            .bold{
                font-weight: bold;
            }
            .gris{
                background: #d9d9d9;
            }
            .container-image{
                background: #e9ebee;
                text-align: center;
            }
            .container-image img{
                vertical-align: middle;
            }
        </style>
      </head>
  <body>
      <div style="text-align: right; text-decoration: underline; padding: 2px">OBLIGACION: {{ $idEstudio }}</div>      
      <div>
      <table class="margin-top-20" style="border: none; padding: 0;">          
          <tr>
              <td>
                  <table style="width: 98%" class="border">
                      <tr>
                          <td class="container-image">
                              <img style="width: 120px;" src="{{ asset('assets/layouts/layout5/img/logosistema.png') }}" title="Logo sistema" alt="Logo sistema">
                          </td>
                          <td style="padding: 0">
                              <table>
                                  <tr class="caption text-center">
                                      <td colspan="2">
                                          PLAN DE PAGOS
                                      </td>
                                  </tr>
                                  <tr>
                                      <td class="text-center" style="vertical-align: top">NOMBRE</td>
                                      <td class="text-center">{{ substr(utf8_decode($data["infoEstudio"][0]->nombre)." ".utf8_decode($data["infoEstudio"][0]->apellido), 0, 24) }}</td>
                                  </tr>
                                  <tr>
                                      <td class="text-center">CÉDULA</td>
                                      <td class="text-center">{{ number_format($data["infoEstudio"][0]->cedula, 0, ",", ".") }}</td>
                                  </tr>
                              </table>
                          </td>
                      </tr>                      
                    </table>  
              </td>
              <td>
                  <table style="width: 98%; float: right" class="border">
                        <tr class="caption text-center">
                            <td colspan="4">CONDICIONES DEL CRÉDITO</td>
                        </tr>                   
                        <tr> 
                            <td class="text-center">CRÉDITO</td>
                            <td class="text-center">TASA</td>
                            <td class="text-center">PLAZO</td>
                            <td class="text-center">CUOTA</td>
                        </tr>
                        <tr>
                            <td class="text-center">${{ number_format(round($data["valorCreditoReal"], 0), 0, ",", ".") }}</td>                    
                            <td class="text-center">{{ $data["tasa"]*100 }}%</td>                    
                            <td class="text-center">{{ $data["plazo"] }}</td>                    
                            <td class="text-center">${{ number_format($data["cuota"], 0, ",", ".") }}</td>                    
                        </tr>
                    </table>  
              </td>
          </tr>         
          
          <tr>
              <td>                  
                  <table style="width: 98%" class="plan border">
                        <tr class="caption">
                            <th class="text-center">#</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">CAPITAL</th>
                            <th class="text-center">INTERÉS</th>
                            <th class="text-center">SEGURO</th>                  
                            <th class="text-center">CUOTA</th>
                            <th class="text-center">SALDO</th>
                        </tr>
                        
                        <tr class="gris">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center">${{ number_format(round($data["valorCreditoReal"], 0), 0, ",", ".") }}</td>
                        </tr>                         
                        <?php
                          $plazo = $data["plazo"];       
                          $vlrCreditoBucle = $data["valorCreditoReal"];
                          $fechas = $data["meses"];
                        ?>                        
                        
                        <?php for($cont = 1; $cont <= ($plazo/2) ;$cont++){                            
                                    
                                    $vlrCreditoBucle = round($vlrCreditoBucle, 3);
                                    $interes = round($vlrCreditoBucle*($data["tasa"]), 3);
                                    $capital = round($data["cuota"] - $interes - $data["seguro"], 3);
                                    $cuotaCalculada = round($interes+$capital + $data["seguro"], 3);
                                    $vlrCreditoBucle = round($vlrCreditoBucle - $capital, 3);                                    
                                ?>

                                <tr class="text-center {{ (($cont % 2) == 0)? 'gris' : '' }}">
                                    <td>{{ $cont }}</td>
                                    <td> <?= (isset($fechas[$cont-1]))? $fechas[$cont-1] : "NA" ?></td>
                                    <td>${{ number_format(round($capital, 0), 0, ",", ".") }}</td>
                                    <td>${{ number_format(round($interes, 0), 0, ",", ".") }}</td>
                                    <td>${{ number_format(round($data["seguro"], 0), 0, ",", ".") }}</td>                                    
                                    <td>${{ number_format(round($cuotaCalculada, 0), 0, ",", ".") }}</td>
                                    <td>${{ number_format(round($vlrCreditoBucle, 0), 0, ",", ".") }}</td>
                                </tr>                                  
                        <?php } ?>
                    </table>
              </td>
              
              <td>
                  <table style="width: 98%; float: right" class="plan text-center border">
                        <tr class="caption">
                            <th class="text-center">#</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">CAPITAL</th>
                            <th class="text-center">INTERÉS</th>
                            <th class="text-center">SEGURO</th>                  
                            <th class="text-center">CUOTA</th>
                            <th class="text-center">SALDO</th>
                        </tr>                        
                        <?php for($i = $cont; $i <= $plazo ;$i++){                   
                                
                            $vlrCreditoBucle = round($vlrCreditoBucle, 3);
                            $interes = round($vlrCreditoBucle*($data["tasa"]), 3);
                            $capital = round($data["cuota"] - $interes - $data["seguro"], 3);
                            $cuotaCalculada = round($interes+$capital+$data["seguro"], 3);
                            $vlrCreditoBucle = round($vlrCreditoBucle - $capital, 3);                                
                        ?>                        
                        <tr class="text-center {{ (($i % 2) == 0)? "gris" : "" }}">
                            <td>{{ $i }}</td>
                            <td><?= (isset($fechas[$i-1]))? $fechas[$i-1] : "NA" ?></td>
                            <td>${{ number_format(round($capital, 0), 0, ",", ".") }}</td>
                            <td>${{ number_format(round($interes, 0), 0, ",", ".") }}</td>
                            <td>${{ number_format(round($data["seguro"], 0), 0, ",", ".") }}</td>                                    
                            <td>${{ number_format(round($cuotaCalculada, 0), 0, ",", ".") }}</td>
                            <td class="{{ ($cont == 1)? "bold" : "" }}">${{ number_format(round($vlrCreditoBucle, 0), 0, ",", ".") }}</td>                  
                        </tr>   
                        <?php } ?>    
                        <tr>
                            <td><span style="visibility: hidden">ASDF</span></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                                    
                            <td></td>
                            <td></td>                  
                        </tr>   
                    </table>
              </td>
          </tr>
      </table>
     </div>  
      
      <?php if($plazo > 98){ ?>
        <div style="page-break-after: always;"></div>
        <!--<div style="page-break-before: always;"></div>-->      
      <?php } ?>
      
      <div class="border text-center" style="font-size: 8px;margin-top: 10px;padding: 2px;">
          ESTE DOCUMENTO NO ES UN CERTIFICADO DE DEUDA, NO ES VÁLIDO PARA PAGAR, CUALQUIER PAGO REALIZADO SIN HABER SOLICITADO EL 
          CORRESPONDIENTE CERTIFICADO DE LA OBLIGACIÓN, SE ENTENDERÁ COMO NO PAGADO. EL PLAN DE PAGOS AQUÍ RELACIONADO TIENE COMO 
          PROPÓSITO EL CONOCIMIENTO DE LAS CONDICIONES DEL CRÉDITO POR PARTE DEL CLIENTE.
      </div>
      
      <table style="width: 60%; margin: 0 auto;margin-top: 10px;">
          <tr>
              <td style="vertical-align: bottom">
                    <div style="margin-top: 50px; border-bottom: 1px solid #e9ebee;width: 300px; height: 2px"></div>
                    <div style="width: 300px;">FIRMA</div>
                    <div style="width: 300px;">FECHA DE ELABORACIÓN {{ date("d/m/Y") }}</div>
              </td>
              <td class="text-center">
                    <div style="width: 80px;height: 80px;margin: 0 auto;border: 1px solid #e9ebee;"></div>
              </td>
          </tr>
      </table>
  </body>
</html>
