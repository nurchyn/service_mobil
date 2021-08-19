<?php defined('BASEPATH') or exit('No direct script access allowed');
class Perhitungan_lib
{
	public function get_data_inputan()
	{
		$arr = [
            'x1' => [0.5, 0, 0.75, 0.25, 1],
            'x2' => [0.25, 0, 1, 0.5, 1],   
            't' => [0.06, 0, 0.44, 1, 1]
        ];

        return $arr;
	}

    public function get_bobot()
	{
		$arr = [
            'v11' => 0.03,
            'v12' => 0.02,   
            'v21' => 0.2,
            'v22' => 0.3,
            'bias1' => 0.7,
            'bias2' => 0.3,
            'w1' => 0.5,
            'w2' => 0.09,
            'b' => 0.31
        ];

        return $arr;
	}

    ##################################################################################

    public function main($arr_inputan = null, $arr_bobot = null)
    {
        if($arr_inputan == null) {
            $arr_inputan = $this->get_data_inputan();
        }

        if($arr_bobot == null) {
            $arr_bobot = $this->get_bobot();
        }
        
        
        $hidden = $this->show_hidden($arr_inputan, $arr_bobot);
        $aktivasi = $this->show_aktivasi($hidden);
        $output = $this->show_output($arr_bobot, $aktivasi);

        echo 'inputan';
        echo "<pre>";
        print_r ($arr_inputan);
        echo "</pre>";
        echo '#####################<br>';
        echo 'bobot';
        echo "<pre>";
        print_r ($arr_bobot);
        echo "</pre>";
        echo '#####################<br>';
        echo 'hidden';
        echo "<pre>";
        print_r ($hidden);
        echo "</pre>";
        echo '#####################<br>';
        echo 'aktivasi';
        echo "<pre>";
        print_r ($aktivasi);
        echo "</pre>";
        echo '#####################<br>';
        echo 'output';
        echo "<pre>";
        print_r ($output);
        echo "</pre>";

        exit;

        return $hidden;
    }

    public function show_hidden($arr_inputan, $arr_bobot)
    {
        for ($i=0; $i < count($arr_inputan['x1']); $i++) { 
            $hdn_z1 = $arr_bobot['bias1'] + ($arr_inputan['x1'][$i] * $arr_bobot['v11']) + ($arr_inputan['x2'][$i] * $arr_bobot['v12']);

            $hdn_z2 = $arr_bobot['bias2'] + ($arr_inputan['x1'][$i] * $arr_bobot['v21']) + ($arr_inputan['x2'][$i] * $arr_bobot['v22']);

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

            $retval['z1'][] = number_format((float)$ak1, 2, '.', '');
            $retval['z2'][] = number_format((float)$ak2, 2, '.', '');
        } 
        
        return $retval;
    }

    public function show_output($arr_bobot, $arr_aktivasi) {
        for ($i=0; $i < count($arr_aktivasi['z1']); $i++) { 
            $y = $arr_bobot['b'] + ($arr_aktivasi['z1'][$i] * $arr_bobot['w1']) + ($arr_aktivasi['z2'][$i] * $arr_bobot['w2']);
            $ak1 = 1/(1+exp(-($y)));

            $retval['y'][] = number_format((float)$y, 3, '.', '');
            $retval['aktivasi'][] = number_format((float)$ak1, 3, '.', '');
        } 

        return $retval;
    }
}
