<?php defined('BASEPATH') or exit('No direct script access allowed');
class Perhitungan_lib
{
	public function get_data_inputan()
	{
		$arr = [
            'x1' => [0.5, 0, 0.75, 0.25, 1],
            'x2' => [0.25, 0, 1, 0.5, 1],   
            't' => [0.0555555555555556, 0, 0.444444444444444, 0.666666666666667, 1],
            'a' => 0.1
        ];

        return $arr;
	}

    public function get_bobot()
	{
		$arr = [
            'v11' => [0.03,0.03,0.03,0.03,0.03],
            'v12' => [0.02,0.02,0.02,0.02,0.02],   
            'v21' => [0.2,0.2,0.2,0.2,0.2,0.2],
            'v22' => [0.3,0.3,0.3,0.3,0.3,0.3],
            'bias1' => [0.7,0.7,0.7,0.7,0.7],
            'bias2' => [0.3,0.3,0.3,0.3,0.3],
            'w1' => [0.5,0.5,0.5,0.5,0.5],
            'w2' => [0.09,0.09,0.09,0.09,0.09],
            'b' => [0.31,0.31,0.31,0.31,0.31],
        ];

        return $arr;
	}

    ##################################################################################

    public function main($arr_inputan = null, $arr_bobot = null, $prev_data=null)
    {
       

        if($arr_inputan == null) {
            $arr_inputan = $this->get_data_inputan();
        }

        if($arr_bobot == null) {
            $arr_bobot = $this->get_bobot();
        }

    
        if($prev_data == null) {
            // if epoch 1 bobot statis
            $hidden = $this->show_hidden($arr_inputan, $arr_bobot);
        }
        else{
            // else bobot dinamis
            $arr_bobot = $this->bobot_input_hidden($prev_data);
            $hidden = $this->show_hidden($arr_inputan, $arr_bobot);
        }
        
        $aktivasi = $this->show_aktivasi($hidden);
        $output = $this->show_output($arr_inputan, $arr_bobot, $aktivasi);
        $mse = $this->show_mse($output);
        
        $retval = [
            'arr_inputan' => $arr_inputan,
            'arr_bobot' => $arr_bobot,
            'aktivasi' => $aktivasi,
            'output' => $output,
            'mse' => $mse
        ];
        
        return $retval;
        
    }

    public function bobot_input_hidden($prev_data)
    {
        for ($i=0; $i < count($prev_data['arr_bobot']['v11']); $i++) { 
            $v11 = $prev_data['arr_bobot']['v11'][$i] + $prev_data['output']['perubahan_bobot_v11'][$i];
            $v12 = $prev_data['arr_bobot']['v12'][$i] + $prev_data['output']['perubahan_bobot_v12'][$i];  
            $v21 = $prev_data['arr_bobot']['v21'][$i] + $prev_data['output']['perubahan_bobot_v21'][$i];
            $v22 = $prev_data['arr_bobot']['v22'][$i] + $prev_data['output']['perubahan_bobot_v22'][$i];
            $bias1 = $prev_data['arr_bobot']['bias1'][$i] + $prev_data['output']['perubahan_bobot_vb1'][$i];
            $bias2 = $prev_data['arr_bobot']['bias2'][$i] + $prev_data['output']['perubahan_bobot_vb2'][$i];
            $w1 =  $prev_data['arr_bobot']['w1'][$i] + $prev_data['output']['perubahan_bobot_w1'][$i];
            $w2 =  $prev_data['arr_bobot']['w2'][$i] + $prev_data['output']['perubahan_bobot_w2'][$i];
            $b =  $prev_data['arr_bobot']['b'][$i] + $prev_data['output']['perubahan_bobot_w_bias'][$i];

            $retval['v11'][] = $v11;
            $retval['v12'][] = $v12;
            $retval['v21'][] = $v21;
            $retval['v22'][] = $v22;
            $retval['bias1'][] = $bias1;
            $retval['bias2'][] = $bias2;
            $retval['w1'][] = $w1;
            $retval['w2'][] = $w2;
            $retval['b'][] = $b;
        }

        return $retval;
        
    }

    public function show_hidden($arr_inputan, $arr_bobot, $bobot_input_hidden = null)
    {
        for ($i=0; $i < count($arr_inputan['x1']); $i++) { 
            
            $hdn_z1 = $arr_bobot['bias1'][$i] + ($arr_inputan['x1'][$i] * $arr_bobot['v11'][$i]) + ($arr_inputan['x2'][$i] * $arr_bobot['v12'][$i]);
            $hdn_z2 = $arr_bobot['bias2'][$i] + ($arr_inputan['x1'][$i] * $arr_bobot['v21'][$i]) + ($arr_inputan['x2'][$i] * $arr_bobot['v22'][$i]);

            $retval['z1'][] = $hdn_z1;
            $retval['z2'][] = $hdn_z2;
        } 
        
        return $retval;
    }

    public function show_aktivasi($arr_hidden)
    {
        for ($i=0; $i < count($arr_hidden['z1']); $i++) { 
            $ak1 = 1/(1+exp(-($arr_hidden['z1'][$i])));
            $ak2 = 1/(1+exp(-($arr_hidden['z2'][$i])));

            $retval['z1_raw'][] = $ak1;
            $retval['z2_raw'][] = $ak2;
            $retval['z1'][] = number_format((float)$ak1, 2, '.', '');
            $retval['z2'][] = number_format((float)$ak2, 2, '.', '');
        } 
        
        return $retval;
    }

    public function show_output($arr_inputan, $arr_bobot, $arr_aktivasi) {
        for ($i=0; $i < count($arr_aktivasi['z1_raw']); $i++) { 
            $y = $arr_bobot['b'][$i] + ($arr_aktivasi['z1_raw'][$i] * $arr_bobot['w1'][$i]) + ($arr_aktivasi['z2_raw'][$i] * $arr_bobot['w2'][$i]);
            $ak1 = 1/(1+exp(-($y)));
            $faktor_error_y  = ($arr_inputan['t'][$i] - $y) * $y * (1-$y);

            $perubahan_bobot_w1 = $arr_inputan['a'] * $arr_aktivasi['z1_raw'][$i] * $faktor_error_y;
            $perubahan_bobot_w2 = $arr_inputan['a'] * $arr_aktivasi['z2_raw'][$i] * $faktor_error_y;
            $perubahan_bobot_w_bias = $arr_inputan['a'] * $faktor_error_y * 1;

            $faktor_error_z_net1  = $faktor_error_y * $arr_bobot['w1'][$i];
            $faktor_error_z_net2  = $faktor_error_y * $arr_bobot['w2'][$i];
            
            $faktor_error_z1 = $faktor_error_z_net1 * $arr_aktivasi['z1_raw'][$i] * (1-$arr_aktivasi['z1_raw'][$i]);
            $faktor_error_z2 = $faktor_error_z_net2 * $arr_aktivasi['z2_raw'][$i] * (1-$arr_aktivasi['z2_raw'][$i]);

            $perubahan_bobot_v11 = $arr_inputan['a'] * $faktor_error_z1 * $arr_inputan['x1'][$i];
            $perubahan_bobot_v12 = $arr_inputan['a'] * $faktor_error_z1 * $arr_inputan['x2'][$i];
            $perubahan_bobot_v21 = $arr_inputan['a'] * $faktor_error_z2 * $arr_inputan['x1'][$i];
            $perubahan_bobot_v22 = $arr_inputan['a'] * $faktor_error_z2 * $arr_inputan['x2'][$i];
            $perubahan_bobot_vb1 = $arr_inputan['a'] * $faktor_error_z1 * 1;
            $perubahan_bobot_vb2 = $arr_inputan['a'] * $faktor_error_z2 * 1;

            $error = $arr_inputan['t'][$i] - $y; 
            $error2 = pow($error, 2);

            $retval['y'][] = number_format((float)$y, 3, '.', '');
            $retval['aktivasi'][] = number_format((float)$ak1, 3, '.', '');
            $retval['faktor_error_y'][] = $faktor_error_y;

            $retval['perubahan_bobot_w1'][] = $perubahan_bobot_w1;
            $retval['perubahan_bobot_w2'][] = $perubahan_bobot_w2;
            $retval['perubahan_bobot_w_bias'][] = $perubahan_bobot_w_bias;  

            $retval['faktor_error_z_net1'][] = $faktor_error_z_net1;
            $retval['faktor_error_z_net2'][] = $faktor_error_z_net2;

            $retval['faktor_error_z1'][] = $faktor_error_z1;
            $retval['faktor_error_z2'][] = $faktor_error_z2;
            
            $retval['perubahan_bobot_v11'][] = $perubahan_bobot_v11;
            $retval['perubahan_bobot_v12'][] = $perubahan_bobot_v12;
            $retval['perubahan_bobot_v21'][] = $perubahan_bobot_v21;
            $retval['perubahan_bobot_v22'][] = $perubahan_bobot_v22;
            $retval['perubahan_bobot_vb1'][] = $perubahan_bobot_vb1;
            $retval['perubahan_bobot_vb2'][] = $perubahan_bobot_vb2;

            $retval['error'][] = $error;
            $retval['error2'][] = $error2;

        } 

        return $retval;
    }

    public function show_mse($arr_output)
    {
       return array_sum($arr_output['error2']) / count($arr_output['error2']);
    }
}
