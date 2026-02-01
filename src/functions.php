<?php

/**
 * Set minimum quantity for specific product (ID: 2412) in cart
 * @param string $product_quantity The HTML for the quantity input field.
 * @param string $cart_item_key The cart item key.
 * @param array $cart_item The cart item data.
 * @return string Modified HTML for the quantity input field.
 */
function sitename_min_qty_for_point_buy_product_in_cart($product_quantity, $cart_item_key, $cart_item)
{

  $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

  $min = 0;

  // Set minimum quantity to 100 for product with ID 2412
  if (2412 === $_product->get_id()) {
    $min = 100;
  }

  $product_quantity = woocommerce_quantity_input(array(
    'input_name'   => "cart[{$cart_item_key}][qty]",
    'input_value'  => $cart_item['quantity'],
    'max_value'    => $_product->get_max_purchase_quantity(),
    'min_value'    => $min,
    'product_name' => $_product->get_name(),
  ), $_product, false);

  return $product_quantity;
}
add_filter('woocommerce_cart_item_quantity', 'sitename_min_qty_for_point_buy_product_in_cart', 99, 3);


/**
 * Handle cart logic for special product (ID: 2412)
 * If user adds any other product, remove the special product (2412) from cart
 * @param string $cart_item_key The cart item key.
 * @param int $product_id The ID of the product being added to the cart.
 * @return void
 */
function sitename_handle_special_product_2412_cart_logic($cart_item_key, $product_id)
{
  $special_product_id = 2412;

  // If user adds the special product (2412)
  if ($product_id == $special_product_id) {
    foreach (WC()->cart->get_cart() as $key => $item) {
      if ($item['product_id'] != $special_product_id) {
        WC()->cart->remove_cart_item($key);
      }
    }

    return;
  }

  // Check if the special product (2412) is in the cart
  foreach (WC()->cart->get_cart() as $key => $item) {
    if ($item['product_id'] == $special_product_id) {
      WC()->cart->remove_cart_item($key);
      return;
    }
  }
}
add_action('woocommerce_add_to_cart', 'sitename_handle_special_product_2412_cart_logic', 99, 6);


/**
 * Hide Specific Gateway when a specfic Product is in Cart
 * @param array $gateways Available payment gateways.
 * @return array Modified payment gateways.
 */
function sitename_hide_point_gateway_for_point_product($gateways)
{
  foreach (WC()->cart->get_cart() as $cart_item) {
    if ($cart_item['product_id'] == 2412) {
      unset($gateways['your-gateway-id']); // Replace 'your-gateway-id' with the actual gateway ID to hide
      break;
    }
  }

  return $gateways;
}

add_filter('woocommerce_available_payment_gateways', 'sitename_hide_point_gateway_for_point_product', 99, 1);
