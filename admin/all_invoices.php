<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class WP360INVOICE_Invoices_List_Table extends WP_List_Table {
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->get_invoices_data();
    }
    function get_invoices_data() {
        $data = array();
        $invoicesArg = array(
            'post_type'      => 'wp360_invoice',
            'posts_per_page' => -1, // Retrieve all posts
        );
        if(isset($_GET['orderby']) && !empty($_GET['orderby'])){
            $invoicesArg['orderby'] = sanitize_text_field($_GET['orderby']);
            if(isset($_GET['order']) && !empty($_GET['order'])){
                $invoicesArg['order'] = sanitize_text_field($_GET['order']);
            }
        }
        $invoices = new WP_Query($invoicesArg);
        foreach ($invoices->posts as $invoice) {
            $user_id = get_post_meta($invoice->ID, 'invoice_user', true);
            $user_info = get_userdata($user_id);
            $invoice_amount = floatval(get_post_meta($invoice->ID, 'invoice_amount', true));
            $data[] = array(
                'ID'               => $invoice->ID,
                'invoice_number'   => sanitize_text_field(get_post_meta($invoice->ID, 'invoice_number', true)),
                'user'             => $user_info ? $user_info->display_name . ' (' . $user_info->user_email . ')' : 'N/A',
                'invoice_title'    => $invoice->post_title,
                'invoice_amount'   => $invoice_amount,
                'invoice_type'     => ucfirst(sanitize_text_field(get_post_meta($invoice->ID, 'invoice_type', true))),
            );
        }
        return $data;
    }
    function get_columns() {
        return array(
            'cb'                => '<input type="checkbox" />',
            'invoice_number'    => esc_html__('Invoice Number', 'wp360-invoice'),
            'user'              => esc_html__('User', 'wp360-invoice'),
            'invoice_title'     => esc_html__('Invoice Title', 'wp360-invoice'),
            'invoice_amount'    => esc_html__('Amount', 'wp360-invoice'),
            'invoice_type'      => esc_html__('Invoice Type', 'wp360-invoice'),
        );
    }
    function get_sortable_columns() {
        return array(
            'invoice_number'   => array('invoice_number', false),
            'user'             => array('user', false),
            'invoice_title'    => array('invoice_title', true),
        );
    }
    function column_default($item, $column_name) {
        return $item[$column_name];
    }
    function column_cb($item) {
        return '<input type="checkbox" name="invoice[]" value="' . esc_attr($item['ID']) . '" />';
    }
    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }
    function process_bulk_action() {
        if (isset($_POST['_wpnonce_bulk_invoice']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce_bulk_invoice']), 'bulk-invoice-nonce-action')) {
            if ('delete' === $this->current_action()) {
                $invoices_to_delete = isset($_REQUEST['invoice']) ? array_map( 'esc_attr',$_REQUEST['invoice']) : array();
                foreach ($invoices_to_delete as $invoiceID) {
                    $post_data = array(
                        'ID'          => $invoiceID,
                        'post_status' => 'deleted_invoice',
                    );
                    wp_update_post($post_data);
                }
                echo '<div class="updated"><p>' . esc_html__('Invoices deleted successfully!', 'wp360-invoice') . '</p></div>';
            }
        } elseif (isset($_POST['_wpnonce_bulk_invoice'])) {
            echo '<div class="error"><p>' . esc_html__('Security check failed.', 'wp360-invoice') . '</p></div>';
        }
    }

}

// Usage: Create an instance of your custom list table and display it
function wp360invoice_display_invoices_list_table() {
    $invoices_list_table = new WP360INVOICE_Invoices_List_Table();
    $invoices_list_table->process_bulk_action();
    $invoices_list_table->prepare_items();
    $invoices_list_table->display();
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('wp360 invoices', 'wp360-invoice');?></h1>
    <a href="javascript:;" onclick="wp360toggleCustomFun('.toggleNewInvoice')" class="page-title-action"><?php esc_html_e('Add New Invoice', 'wp360-invoice');?></a>
    <div class="toggleNewInvoice" style="display:none;">
        <?php require_once('add_invoice.php'); ?>
    </div>
    <form method="post">
        <?php      
            wp_nonce_field('bulk-invoice-nonce-action', '_wpnonce_bulk_invoice');
            wp360invoice_display_invoices_list_table();
        ?>
    </form>
</div>

