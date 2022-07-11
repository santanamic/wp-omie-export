<?php
if (!function_exists('omie_export_get_transaction_id_by_order'))
{

    function omie_export_get_transaction_id_by_order($order_id)
    {
        $order = wc_get_order($order_id);

        if ($order->parent_id != 0)
        {
            $parent_id = $order->parent_id;
            $order = wc_get_order($parent_id);
        }

        $transaction_id = $order->get_meta('_wc_pagarme_transaction_id');

        if (false != $transaction_id || !is_null($transaction_id))
        {
            return $transaction_id;
        }

        return null;
    }

}

if (!function_exists('omie_export_get_order_by_transaction_id'))
{

    function omie_export_get_order_by_transaction_id($transaction_id)
    {
        if (!is_null($transaction_id))
        {
            global $wpdb;

            $order_id = absint($wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wc_pagarme_transaction_id' AND meta_value = %d", $transaction_id)));
            $order = wc_get_order($order_id);

            if (false != $order && !empty($order))
            {
                return $order;
            }
            else
            {
                return null;
            }
        }

        return null;
    }

}

if (!function_exists('omie_export_request_data'))
{
    function omie_export_request_data()
    {		
        $filter_args = array();

		if( $filter_specific_ids = $_POST['omie-export-filter-specific-users'] ) { 
			$filter_specific_ids = array_flip( explode( ',', $filter_specific_ids ) );
		}

		if( $filter_start_order_number = $_POST['omie-export-start-filter-id'] ) { 
			$filter_args['metadata'] = array(
				'order_number' => '>=' . $filter_start_order_number
			);
		}

		if( $filter_by_start_date = $_POST['omie-export-start-date'] ) {
			$filter_args['date_created[0]'] = '>=' . date_create(
				$filter_by_start_date, timezone_open('America/Sao_Paulo')
			)->getTimestamp() * 1000;
		}
		
		if( $filter_by_end_date = $_POST['omie-export-end-date']) {
			$filter_args['date_created[1]'] = '<=' . date_create(
				sprintf('%sT23:59:59', 
					$filter_by_end_date
				) , timezone_open('America/Sao_Paulo'))->getTimestamp() * 1000;
		}

		omie_export_get_dokan_data($filter_args, $filter_specific_ids, $_POST);
	}
}

if (!function_exists('omie_export_get_dokan_data'))
{

    function omie_export_get_dokan_data($filter, $filter_specific_ids = array(), $request)
    {
        $data = [];
		
        //transaction resquest args
        $args = array_merge(array(
            'count' => 1000,
            'status' => 'paid',
        ) , $filter);
		
        $transactions = omie_export_get_transaction_data($args);

        if ($transactions)
        {
            foreach ($transactions as $transaction)
            {
                if ($transaction['split_rules'])
                {
                    foreach ($transaction['split_rules'] as $split_rules)
                    {
						
						if( $user = omie_export_get_saller_by_recipienteId($split_rules['recipient_id']) )
                        {
							$seller = $user->get('dokan_profile_settings');
							
							if( !empty( $request['omie-export-start-date'] ) ) 
							{
								$is_new_seller = strtotime( 
									$user->get('user_registered')) > strtotime($request['omie-export-start-date'] 
								);
							} else 
							{
								$is_new_seller = false;
							}

							$recipient = reset(omie_export_get_recipient_data(array(
								'id' => $split_rules['recipient_id']
							)));
							
							if( $filter_specific_ids && !isset($filter_specific_ids[$user->ID]) )
							{
								continue;
							}
							
							if (!isset($data[$user->ID]))
							{
								$data[$user->ID]['seller'] = array(
									'id' => $user->ID,
									'registered' => $user->get('user_registered'),
									'is_new' => $is_new_seller,
									'legal_name' => $recipient['bank_account']['legal_name'],
									'bank_code' => $recipient['bank_account']['bank_code'],
									'agencia' => $recipient['bank_account']['agencia'],
									'agencia_dv' => $recipient['bank_account']['agencia_dv'],
									'conta' => $recipient['bank_account']['conta'],
									'conta_dv' => $recipient['bank_account']['conta_dv'],
									'type' => $recipient['bank_account']['type'],
									'document_type' => $recipient['bank_account']['document_type'],
									'document_number' => $recipient['bank_account']['document_number'],
									'date_created' => $recipient['date_created'],
									'store_name' => $seller['store_name'],
									'email' => $user->get('user_email') ,
									'phone' => $seller['phone'],
									'address' => array(
										'street_1' => $seller['address']['street_1'],
										'street_2' => $seller['address']['street_2'],
										'city' => $seller['address']['city'],
										'zip' => $seller['address']['zip'],
										'country' => $seller['address']['country'],
										'state' => $seller['address']['state'],
									) ,
								);
							}
							$data[$user->ID]['transactions'][] = array(
								'order_number' => $transaction['metadata']['order_number'],
								'status' => $transaction['status'],
								'date_created' => $transaction['date_created'],
								'payment_method' => $transaction['payment_method'],
								'paid_amount' => $transaction['paid_amount'],
								'installments' => $transaction['installments'],
								'cost' => $transaction['cost'],
								'split_amount' => $split_rules['amount'],
							);
							$data[$user->ID]['data']['split_total_amount'] += $split_rules['paid_amount'] - $split_rules['split_amount'];
							$data[$user->ID]['data']['split_os_description'] .= "Número do pedido: {$transaction['metadata']['order_number']} \n";                        
						}
                    }
                }
            }
        }

        omie_export_data($data, $request);
    }

}

if (!function_exists('omie_export_get_dokan_all_orders'))
{

    function omie_export_get_dokan_all_orders($order_id, $order)
    {
        $all_orders = [];
        $has_suborder = get_post_meta($order_id, 'has_sub_order', true);

        if ($has_suborder == '1')
        {
            $sub_orders = get_children(array(
                'post_parent' => $order_id,
                'post_type' => 'shop_order'
            ));
            foreach ($sub_orders as $order_post)
            {
                $sub_order = wc_get_order($order_post->ID);
                $all_orders[] = $sub_order;
            }

        }
        else
        {
            $all_orders[] = $order;
        }

        return $all_orders;
    }

}

if (!function_exists('omie_export_do_request'))
{
    function omie_export_do_request($endpoint, $method = 'POST', $data = array() , $headers = array())
    {

        $params = array(
            'method' => $method,
            'timeout' => 60,
        );

        if (!empty($headers))
        {
            $params['headers'] = $headers;
        }

        if (!empty($data))
        {
            $params['body'] = $data;
        }

        return wp_safe_remote_post('https://api.pagar.me/1/' . $endpoint, $params);
    }
}

if (!function_exists('omie_export_get_transaction_data'))
{
    function omie_export_get_transaction_data($args = array())
    {
        $response = omie_export_do_request('transactions/', 'GET', array_merge(array(
            'api_key' => 'ak_test_TudwDN44zu9K9ukIh4Hw8Eol38FnKP'
        ) , $args));

        if (!is_wp_error($response))
        {
            $data = json_decode($response['body'], true);

            if (isset($data['errors']))
            {
                return $data;
            }

            return $data;
        }
    }
}

if (!function_exists('omie_export_get_recipient_data'))
{
    function omie_export_get_recipient_data($args = array())
    {
        $response = omie_export_do_request('recipients/', 'GET', array_merge(array(
            'api_key' => 'ak_test_TudwDN44zu9K9ukIh4Hw8Eol38FnKP'
        ) , $args));

        if (!is_wp_error($response))
        {
            $data = json_decode($response['body'], true);

            if (isset($data['errors']))
            {
                return $data;
            }

            return $data;
        }
    }
}

if (!function_exists('omie_export_get_seller_order'))
{

    function omie_export_get_seller_order($seller_id = null, $parent_order = null)
    {

        if ($seller_id != null && $parent_order != null)
        {

            $all_orders = omie_export_get_dokan_all_orders($parent_order->ID, $parent_order);

            foreach ($all_orders as $order)
            {
                $order_id = dokan_get_prop($order, 'id');
                $order_seller_id = dokan_get_seller_id_by_order($order_id);

                if ($order_seller_id == $seller_id)
                {
                    return $order;
                }
            }
        }

        return null;
    }

}

if (!function_exists('omie_export_payables_filter_by_day'))
{

    function omie_export_payables_filter_by_day($payables, $seller_id)
    {
        $payables_data = [];
        $total_amount = 0;

        foreach ($payables as $payable)
        {
            if ($payable['type'] == 'credit')
            {
                $order_id = (omie_export_get_seller_order($seller_id, omie_export_get_order_by_transaction_id($payable['transaction_id'])))->ID;
                $payment_date = date('Y-m-d', strtotime($payable['payment_date']));
                $payables_data[$payment_date]['type']['status'] = $payable['status'];
                $payables_data[$payment_date]['type']['total'] += ($payable['amount'] - $payable['fee']);
                $payables_data[$payment_date]['type']['transactions'][] = array(
                    'date_created' => date('Y-m-d', strtotime($payable['date_created'])) ,
                    'payment_date' => $payment_date,
                    'payment_method' => $payable['payment_method'],
                    'installment' => $payable['installment'],
                    'amount' => $payable['amount'] - $payable['fee'],
                    'status' => $payable['status'],
                    'transaction_id' => $payable['transaction_id'],
                    'order' => !is_null($order_id) ? $order_id : null,
                    'order_link' => !is_null($order_id) ? esc_url(wp_nonce_url(add_query_arg(array(
                        'order_id' => $order_id
                    ) , dokan_get_navigation_url('orders')) , 'dokan_view_order')) : null,
                );

                if ('waiting_funds' == $payable['status'])
                {
                    $total_amount += ($payable['amount'] - $payable['fee']);
                }
            }
        }

        return array(
            'total' => $total_amount,
            'transactions' => $payables_data
        );
    }

}

if (!function_exists('omie_export_get_recipienteId_by_saller'))
{

    function omie_export_get_recipienteId_by_saller($seller_id)
    {
        $recipiente_id = get_user_meta($seller_id, 'pagarme_recipiente_id', true); //id_recipiente_pagarme
        if (!empty($recipiente_id))
        {
            return $recipiente_id;
        }

        return null;
    }

}

if (!function_exists('omie_export_get_saller_by_recipienteId'))
{

    function omie_export_get_saller_by_recipienteId($recipiente_id)
    {
        $saller = reset(get_users(array(
            'meta_key' => 'pagarme_recipiente_id',
            'meta_value' => $recipiente_id
        )));

        return $saller;
    }

}

if (!function_exists('omie_export_output_filename'))
{

    function omie_export_output_filename( $filename = false ) {
		$replacements = array(
			'%y' => date('d'),
			'%m' => date('m'),
			'%d' => date('Y'),
		);

		return strtr( $filename, $replacements );
	}
}

if (!function_exists('omie_export_output_data'))
{
    function omie_export_output_format( $value = [], $request ) {
		$limit     = intval( $request['omie-export-os-group-number'] );
		$registens = [];

		foreach( $value as $key => $data ) {
			$index = 0;
			$count = 0;
			$list  = [];
			$split = [];

			$registensw[] = $data;

			foreach( $data['transactions'] as $key => $transaction ) { 
				if( $count === $limit ) {
					$count = 0; $index++; $split = [];
				}
				
				$list[$index]['seller'] = $data['seller'];
				$list[$index]['transactions'][] = $transaction;
				$list[$index]['data']['split_total_amount'] += $transaction['paid_amount'] - $transaction['split_amount'];
				$list[$index]['data']['split_os_description'] .= sprintf( "Número do pedido: %d - Taxa de intermediação: R$ %0.2f \n", 
					$transaction['order_number'], ($transaction['paid_amount'] - $transaction['split_amount']) / 100 
				);
				
				$count++;
			}
			
			$registens = array_merge(
				$registens, 
				$list
			);
		}

		$json = json_encode( $registens );
		
		return base64_encode($json);
	}
}

function omie_export_data($data, $request)
{
	$filename = omie_export_output_filename( $request['omie-export-export-filename'] );
	$json = omie_export_output_format($data, $request);
	$output = null;
	
	chdir(OMIE_EXPORT_PATH . '/bin');
	
	if( false !== strpos(ini_get('disable_functions'), 'exec') ) { 
		die('A funcão exec do PHP não está habilitada no servidor');
	}
	
	exec("json2excel -json {$json} 2>&1", $output); 
	
	if( 'Success' != end($output) ) {
		die(sprintf( 'Houve um erro na geração do arquivo de saída: %s', 
			var_export($output, true) 
		));
	}
	
    if (!class_exists('ZipArchive', false)) {
        die('Não foi possível carregar a classe: ZipArchive');
    }
	
	$zip = new ZipArchive();
	$zip_file_tmp = tempnam('/tmp', 'zip');
	$zip->open($zip_file_tmp, ZipArchive::OVERWRITE);
	
	if( isset( $request['omie-export-type-os'] ) ) {
		$zip->addFile(OMIE_EXPORT_PATH . '/temp/Ordens_Servico.xlsx', 'Ordens_Servico.xlsx');
	}

	if( isset( $request['omie-export-type-cli'] ) ) {
		$zip->addFile(OMIE_EXPORT_PATH . '/temp/Clientes_Fornecedores.xlsx', 'Clientes_Fornecedores.xlsx');
	}
	
	$zip->close();
    
	header('Content-Type: application/zip');
    header('Content-Description: File Transfer');
	header('Content-disposition: attachment; filename='.$filename);
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    flush();
	readfile($zip_file_tmp);
   	exit(1);
}

