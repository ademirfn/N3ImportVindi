<?php 


require( dirname(__FILE__) . '/wp-load.php' );


$old_order  = wc_get_order(482);
$product_id = 6570;

// Pega o id da Assinatura na Vindi como metadado na Order
$vindi_subscription_id = get_post_meta($old_order->id, 'vindi_wc_subscription_id', true);

// Adicionar indicador do ciclo que a order representava como metadado
add_post_meta($old_order->id, 'vindi_wc_cycle', 2);//Para criar a Subscription no WCS
WC_Subscriptions_Manager::create_pending_subscription_for_order($old_order, $product_id);

//Obs: Depois disso ainda é necessário vincular o ID dessa Subscription no código externo (code) da Assinatura na Vindi.

//2º Atualiza o id da Assinatura na Vindi com o metadado na Subscription criada no WCS.

// Pega a Subscription do WCS criada anteriormente
$subscriptions = wcs_get_subscriptions(array(
  'order_id'   => $order->id,
  'product_id' => $product_id
));
$subscription = end($subscriptions);// Adiciona o metadado com o id da assinatura da vindi pego anteriormente

add_post_meta($subscription->id, 'vindi_wc_subscription_id', $vindi_subscription_id);

//3º Caso o período vigente já esteja criado na Vindi, precisamos renovar a Subscription dentro do WCS manualmente para criar um nova Order.

// Cria a próxima order
WC_Subscriptions_Manager::prepare_renewal($subscription->id);

// Pega a nova order criada

$new_order = wc_get_order($subscription->get_last_order());//Adiciona o ciclo correspondente
add_post_meta($old_order->id, 'vindi_wc_cycle', 3);

?>

