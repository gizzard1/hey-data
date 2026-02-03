<?php

namespace App\Http\Controllers;

use App\Models\walog;
use Illuminate\Http\Request;

class WaController extends Controller
{
    public function envia(Request $request, $uid)
    {
        $data = $request->all();

        $token = $data['token'];
        $url = $data['url'];

        if($data['type']=='confirmacion_cita'){
            $mensaje = $this->generarConfirmacionCita($data,$uid);
        }elseif($data['type']=='solicitud_encuesta'){
            $mensaje = $this->generarSolicitudEncuesta($data,$uid);
        }

        $header = [
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($curl), true);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Verifica si la solicitud fue exitosa
        if ($status_code == 200 && isset($data['cita_id'])) {
            walog::create([
                'uid'=>$uid,
                'sent'=>true,
                'cita_id'=>$data['cita_id'],
                'type'=>'confirmar_cita'
            ])->save();
        }elseif ($status_code == 200 && isset($data['venta_id'])) {
            walog::create([
                'uid'=>$uid,
                'sent'=>true,
                'venta_id'=>$data['venta_id'],
                'type'=>'enviar_encuesta'
            ])->save();
        } else {
            walog::create([
                'uid'=>$uid,
                'sent'=>false,
                'cita_id'=>null,
                'venta_id'=>null,
                'type'=>'error'
            ])->save();
        }

        return redirect()->to(route('/'));
    }

    private function generarConfirmacionCita($data,$uid)
    {
        $mensaje = json_encode([
            'messaging_product' => 'whatsapp',
            'to' => $data['phone'],
            'type' => 'template',
            'template' => [
                'name' => 'confirmacion_cita',
                'language' => [
                    'code' => 'es',
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['salon_name'],
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['date'],
                            ],
                            [
                                'type' => 'text',
                                'text' => $data['time'],
                            ],
                            [
                                'type' => 'text',
                                'text' => $data['services'],
                            ]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => 'aceptacion-cita/' . $uid,
                            ]
                        ]
                    ]
                ]
            ],
        ]);
        return $mensaje;
    }
    private function generarSolicitudEncuesta($data,$uid)
    {
        $mensaje = json_encode([
            'messaging_product' => 'whatsapp',
            'to' => $data['phone'],
            'type' => 'template',
            'template' => [
                'name' => 'feedback_survey_1',
                'language' => [
                    'code' => 'es',
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['cust_name'],
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['mov_type'],
                            ],
                        ]
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => 'encuesta/' . $uid,
                            ]
                        ]
                    ]
                ]
            ],
        ]);
        return $mensaje;
    }
    public function aceptarCita($uid)
    {
        $walog = walog::with('date.salon')->where('uid',$uid)->first();
        $date = $walog->date;
        if($date!==null){
            if(isset($date) && $date->status == 'Agendada'){
                $date->status = 'Confirmada';
                $date->save();
            }
            if($date->salon->web_asociada){
                return redirect()->to($date->salon->web_asociada);
            }
        }
        return redirect()->to('cita-confirmada');
    }
}
