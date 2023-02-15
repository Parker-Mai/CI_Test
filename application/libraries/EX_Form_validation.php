<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EX_Form_validation extends CI_Form_validation {

	public function set_rules_v2($field, $label = '', $rules = array(), $errors = array())
	{

		if ($this->CI->input->method() !== 'post' && empty($this->validation_data))
		{
			return $this;
		}

		if (is_array($field))
		{

			//差別在於做資料處理而已
			$afterData = [];
			foreach ($field as $field => $detail) {
				
				$afterData[] = [
					'field' => $field,
					'label' => $detail['label'],
					'rules' => $detail['rules'],
					'errors' => $detail['errors'],
				];

			}

			$field = $afterData;

			foreach ($field as $row)
			{

				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				$label = isset($row['label']) ? $row['label'] : $row['field'];

				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
		}

		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		$label = ($label === '') ? $field : $label;

		$indexes = array();

		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}

	/*
	 * 台灣手機格式驗證
	 * 三種格式
	 * 1. 09xxxxxxxx (預設)
	 * 2. 09xx-yyyyyy
	 * 3. 09xx-yyy-zzz
	 */
	public function valid_phone_tw($str ,$val)
	{
		$chk_result = FALSE;
        // 電話必須要有值
        if (!empty(trim($str))) {//變數打錯
            // 驗證val只能是 1 2 3
            if (in_array($val, [1, 2, 3])) {

                switch ($val) {
                    case 1:
                        $pattern = "/^09[0-9]{8,8}$/";
                        break;
                    case 2:
                        $pattern = "/^09[0-9]{2,2}-[0-9]{6,6}$/";
                        break;
                    case 3:
                        $pattern = "/^09[0-9]{2,2}-[0-9]{3,3}-[0-9]{3,3}$/";
                        break;
                }
                if (preg_match($pattern, $str)) {
                    $chk_result = TRUE;
                }

            }

        }

		return $chk_result;

	}

}
