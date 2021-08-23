<?php defined('BASEPATH') or exit('No direct script access allowed');
class Perhitungan_lib extends CI_Controller
{

    function __construct(){
		$this->_ci = &get_instance();
		$this->_ci->load->model('m_global');  //<-------Load the Model first
    }

	public function get_data_inputan()
	{
		$arr = [
            'x1' => [0.5, 0, 0.75, 0.25, 1], //dari kolom t_kendaraan_masuk.hitung_pekrtjaan
            'x2' => [0.25, 0, 1, 0.5, 1],   //dari kolom t_kendaraan_masuk.hitung_onderdil
            't' => [0.0555555555555556, 0, 0.444444444444444, 0.666666666666667, 1], //lama servis satuan apa ?
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

    public function normalisasi($arr_inputan)
    {
        $norm_min = 0;
        $min_x1 = min($arr_inputan['x1']);
        $min_x2 = min($arr_inputan['x2']);
        $min_t = min($arr_inputan['t']);

        $norm_max = 1;
        $max_x1 = max($arr_inputan['x1']);
        $max_x2 = max($arr_inputan['x2']);
        $max_t = max($arr_inputan['t']);
        

        for ($i=0; $i < count($arr_inputan['x1']); $i++) {  
           
            $norm_x1 = @((($arr_inputan['x1'][$i] - $min_x1) / ($max_x1 - $min_x1)) * ($norm_max - $norm_min)) + $norm_min;
            if (is_nan($norm_x1)) {
                $norm_x1 =  0; 
            }
            $norm_x2 = @((($arr_inputan['x2'][$i] - $min_x2) / ($max_x2 - $min_x2)) * ($norm_max - $norm_min)) + $norm_min;
            if (is_nan($norm_x2)) {
                $norm_x2 =  0; 
            }
            $norm_t = @((($arr_inputan['t'][$i] - $min_t) / ($max_t - $min_t)) * ($norm_max - $norm_min)) + $norm_min;
            if (is_nan($norm_t)) {
                $norm_t =  0; 
            }
            
            // $retval['x1'][] = number_format((float)$norm_x1,2);
            // $retval['x2'][] = number_format((float)$norm_x2,2);
            // $retval['t'][] = number_format((float)$norm_t,2);
            $retval['x1'][] = (float)$norm_x1;
            $retval['x2'][] = (float)$norm_x2;
            $retval['t'][] = (float)$norm_t;
            $retval['a'] = $arr_inputan['a'];
        }

        return $retval;
    }

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

        
        // echo "<pre>";
        // print_r ($retval);
        // echo "</pre>";
        // exit;
        
        return $retval;
        
    }

    public function bobot_input_hidden($prev_data)
    {
        for ($i=0; $i < count($prev_data['arr_bobot']['v11']); $i++) { 
            $v11 = number_format((float)$prev_data['arr_bobot']['v11'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_v11'][$i], 5, '.', '');
            $v12 = number_format((float)$prev_data['arr_bobot']['v12'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_v12'][$i], 5, '.', '');  
            $v21 = number_format((float)$prev_data['arr_bobot']['v21'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_v21'][$i], 5, '.', '');
            $v22 = number_format((float)$prev_data['arr_bobot']['v22'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_v22'][$i], 5, '.', '');
            $bias1 = number_format((float)$prev_data['arr_bobot']['bias1'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_vb1'][$i], 5, '.', '');
            $bias2 = number_format((float)$prev_data['arr_bobot']['bias2'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_vb2'][$i], 5, '.', '');
            $w1 =  number_format((float)$prev_data['arr_bobot']['w1'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_w1'][$i], 5, '.', '');
            $w2 =  number_format((float)$prev_data['arr_bobot']['w2'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_w2'][$i], 5, '.', '');
            $b =  number_format((float)$prev_data['arr_bobot']['b'][$i], 5, '.', '') + number_format((float)$prev_data['output']['perubahan_bobot_w_bias'][$i], 5, '.', '');

            // $v11 = $prev_data['arr_bobot']['v11'][$i] + $prev_data['output']['perubahan_bobot_v11'][$i];
            // $v12 = $prev_data['arr_bobot']['v12'][$i] + $prev_data['output']['perubahan_bobot_v12'][$i];  
            // $v21 = $prev_data['arr_bobot']['v21'][$i] + $prev_data['output']['perubahan_bobot_v21'][$i];
            // $v22 = $prev_data['arr_bobot']['v22'][$i] + $prev_data['output']['perubahan_bobot_v22'][$i];
            // $bias1 = $prev_data['arr_bobot']['bias1'][$i] + $prev_data['output']['perubahan_bobot_vb1'][$i];
            // $bias2 = $prev_data['arr_bobot']['bias2'][$i] + $prev_data['output']['perubahan_bobot_vb2'][$i];
            // $w1 =  $prev_data['arr_bobot']['w1'][$i] + $prev_data['output']['perubahan_bobot_w1'][$i];
            // $w2 =  $prev_data['arr_bobot']['w2'][$i] + $prev_data['output']['perubahan_bobot_w2'][$i];
            // $b =  $prev_data['arr_bobot']['b'][$i] + $prev_data['output']['perubahan_bobot_w_bias'][$i];

            // $v11 = bcadd($prev_data['arr_bobot']['v11'][$i], $prev_data['output']['perubahan_bobot_v11'][$i], 5);
            // $v12 = bcadd($prev_data['arr_bobot']['v12'][$i], $prev_data['output']['perubahan_bobot_v12'][$i], 5);  
            // $v21 = bcadd($prev_data['arr_bobot']['v21'][$i], $prev_data['output']['perubahan_bobot_v21'][$i], 5);
            // $v22 = bcadd($prev_data['arr_bobot']['v22'][$i], $prev_data['output']['perubahan_bobot_v22'][$i], 5);
            // $bias1 = bcadd($prev_data['arr_bobot']['bias1'][$i], $prev_data['output']['perubahan_bobot_vb1'][$i], 5);
            // $bias2 = bcadd($prev_data['arr_bobot']['bias2'][$i], $prev_data['output']['perubahan_bobot_vb2'][$i], 5);
            // $w1 =  bcadd($prev_data['arr_bobot']['w1'][$i], $prev_data['output']['perubahan_bobot_w1'][$i], 5);
            // $w2 =  bcadd($prev_data['arr_bobot']['w2'][$i], $prev_data['output']['perubahan_bobot_w2'][$i], 5);
            // $b =  bcadd($prev_data['arr_bobot']['b'][$i], $prev_data['output']['perubahan_bobot_w_bias'][$i], 5);

            $retval['v11'][] = number_format((float)$v11, 5, '.', '');
            $retval['v12'][] = number_format((float)$v12, 5, '.', '');
            $retval['v21'][] = number_format((float)$v21, 5, '.', '');
            $retval['v22'][] = number_format((float)$v22, 5, '.', '');
            $retval['bias1'][] = number_format((float)$bias1, 5, '.', '');
            $retval['bias2'][] = number_format((float)$bias2, 5, '.', '');
            $retval['w1'][] = number_format((float)$w1, 5, '.', '');
            $retval['w2'][] = number_format((float)$w2, 5, '.', '');
            $retval['b'][] = number_format((float)$b, 5, '.', '');
        }

        return $retval;
        
    }

    public function show_hidden($arr_inputan, $arr_bobot, $bobot_input_hidden = null)
    {
        for ($i=0; $i < count($arr_inputan['x1']); $i++) { 
            
            $hdn_z1 = $arr_bobot['bias1'][$i] + ($arr_inputan['x1'][$i] * $arr_bobot['v11'][$i]) + ($arr_inputan['x2'][$i] * $arr_bobot['v12'][$i]);
            $hdn_z2 = $arr_bobot['bias2'][$i] + ($arr_inputan['x1'][$i] * $arr_bobot['v21'][$i]) + ($arr_inputan['x2'][$i] * $arr_bobot['v22'][$i]);

            $retval['z1'][] = number_format((float)$hdn_z1, 5, '.', '');
            $retval['z2'][] = number_format((float)$hdn_z2, 5, '.', '');
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
        for ($i=0; $i < count($arr_aktivasi['z1']); $i++) { 
            // $y = $arr_bobot['b'][$i] + ($arr_aktivasi['z1'][$i] * $arr_bobot['w1'][$i]) + ($arr_aktivasi['z2'][$i] * $arr_bobot['w2'][$i]);
            $y = $arr_bobot['b'][$i] + (bcmul($arr_aktivasi['z1'][$i], $arr_bobot['w1'][$i])) + (bcmul($arr_aktivasi['z2'][$i], $arr_bobot['w2'][$i]));
            $ak1 = 1/(1+exp(-($y)));
            $faktor_error_y  = ($arr_inputan['t'][$i] - $y) * $y * (1-$y);

            $perubahan_bobot_w1 = $arr_inputan['a'] * $arr_aktivasi['z1'][$i] * $faktor_error_y;
            $perubahan_bobot_w2 = $arr_inputan['a'] * $arr_aktivasi['z2'][$i] * $faktor_error_y;
            $perubahan_bobot_w_bias = $arr_inputan['a'] * $faktor_error_y * 1;

            $faktor_error_z_net1  = $faktor_error_y * $arr_bobot['w1'][$i];
            $faktor_error_z_net2  = $faktor_error_y * $arr_bobot['w2'][$i];
            
            $faktor_error_z1 = $faktor_error_z_net1 * $arr_aktivasi['z1'][$i] * (1-$arr_aktivasi['z1'][$i]);
            $faktor_error_z2 = $faktor_error_z_net2 * $arr_aktivasi['z2'][$i] * (1-$arr_aktivasi['z2'][$i]);

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
            $retval['faktor_error_y'][] = number_format((float)$faktor_error_y, 5, '.', '');

            $retval['perubahan_bobot_w1'][] = number_format((float) $perubahan_bobot_w1, 5, '.', '');
            $retval['perubahan_bobot_w2'][] = number_format((float) $perubahan_bobot_w2, 5, '.', '');
            $retval['perubahan_bobot_w_bias'][] = number_format((float) $perubahan_bobot_w_bias, 5, '.', '');  

            $retval['faktor_error_z_net1'][] = number_format((float) $faktor_error_z_net1, 5, '.', '');
            $retval['faktor_error_z_net2'][] = number_format((float) $faktor_error_z_net2, 5, '.', '');

            $retval['faktor_error_z1'][] = number_format((float) $faktor_error_z1, 5, '.', '');
            $retval['faktor_error_z2'][] = number_format((float) $faktor_error_z2, 5, '.', '');
            
            $retval['perubahan_bobot_v11'][] = number_format((float) $perubahan_bobot_v11, 5, '.', '');
            $retval['perubahan_bobot_v12'][] = number_format((float) $perubahan_bobot_v12, 5, '.', '');
            $retval['perubahan_bobot_v21'][] = number_format((float) $perubahan_bobot_v21, 5, '.', '');
            $retval['perubahan_bobot_v22'][] = number_format((float) $perubahan_bobot_v22, 5, '.', '');
            $retval['perubahan_bobot_vb1'][] = number_format((float) $perubahan_bobot_vb1, 5, '.', '');
            $retval['perubahan_bobot_vb2'][] = number_format((float) $perubahan_bobot_vb2, 5, '.', '');

            $retval['error'][] = number_format((float) $error, 2, '.', '');
            $retval['error2'][] = number_format((float) $error2, 2, '.', '');

        } 

        return $retval;
    }

    public function show_mse($arr_output)
    {
       return number_format((float) array_sum($arr_output['error2']) / count($arr_output['error2']), 2, '.', '');
    }
}
