<?php
/*
Plugin Name: Custom Contact Form Plugin
Description: A plugin for creating a custom contact form and storing submissions in the database. use shortcode [houzeo_feedback_form] and [houzeo_feedback_list]
Version: 1.0
Author: yogesh
*/


function addjQueryFromCDN() {

    wp_enqueue_script( 'jsdeliver', 'https://code.jquery.com/jquery-3.7.1.min.js' );    
}
add_action( 'wp_enqueue_scripts', 'addjQueryFromCDN' );

function enqueue_custom_jquery() {
    wp_enqueue_script('custom-jquery', plugin_dir_url(__FILE__) . 'js/jquery-3.7.1.min.js', array(), '3.7.1', true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_jquery');

function create_custom_form_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_form_data';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            email text NOT NULL,
            phone text NOT NULL,
            comment text NOT NULL,
            submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

add_action('init', 'create_custom_form_table');

function my_ajax_callback() {
    if (isset($_POST['submit'])) {
        $data = array();
        $data['name'] = sanitize_text_field($_POST['name']);
        $data['email'] = sanitize_email($_POST['email']);
        $data['phone'] = sanitize_text_field($_POST['phone']);
        $data['comment'] = sanitize_text_field($_POST['comment']);

        $response = insert_data_into_database($data);

        if ($response) {
            $response_data = array(
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'comment' => $data['comment'],
            );

            wp_send_json_success($response_data);
        } else {
            wp_send_json_error('Database insertion failed.');
        }

    } else {
        wp_send_json_error('Invalid request!');
    }
    wp_die();
}

add_action('wp_ajax_my_ajax_action', 'my_ajax_callback');
add_action('wp_ajax_nopriv_my_ajax_action', 'my_ajax_callback');

function insert_data_into_database($data) {
    global $wpdb;
    global $table_prefix;
    $table = $table_prefix . 'custom_form_data';

    $tableresult = $wpdb->insert($table, array(
        "name" => $data['name'],
        "email" => $data['email'],
        "phone" => $data['phone'],
        "comment" => $data['comment']
    ));

    return $tableresult;
}

add_action('wp_ajax_fetch_database_data', 'fetch_database_data_callback');
add_action('wp_ajax_nopriv_fetch_database_data', 'fetch_database_data_callback');

function fetch_database_data_callback() {
    global $wpdb;
    global $table_prefix;
    $table = $table_prefix . 'custom_form_data';

    $sql_query = "
        SELECT (@row_number:=@row_number + 1) AS row_number,
               name, email, phone, comment
        FROM $table, (SELECT @row_number:=0) AS rn
    ";

    $data = $wpdb->get_results($sql_query);

    if ($wpdb->last_error) {
        error_log("Database Error: " . $wpdb->last_error);
    }

    if ($data) {
        $response_data = '<table id="database-results-table">';
        $response_data .= '<thead>';
        $response_data .= '<tr>';
        $response_data .= '<th>Row Number</th>';
        $response_data .= '<th>Name</th>';
        $response_data .= '<th>Email</th>';
        $response_data .= '<th>Phone</th>';
        $response_data .= '<th>Comment</th>';
        $response_data .= '</tr>';
        $response_data .= '</thead>';
        $response_data .= '<tbody>';

        foreach ($data as $item) {
            $row_number = esc_html($item->row_number);
            $name = esc_html($item->name);
            $email = esc_html($item->email);
            $phone = esc_html($item->phone);
            $comment = esc_html($item->comment);

            $response_data .= '<tr>';
            $response_data .= '<td>' . $row_number . '</td>';
            $response_data .= '<td>' . $name . '</td>';
            $response_data .= '<td>' . $email . '</td>';
            $response_data .= '<td>' . $phone . '</td>';
            $response_data .= '<td>' . $comment . '</td>';
            $response_data .= '</tr>';
        }

        $response_data .= '</tbody>';
        $response_data .= '</table>';

        wp_send_json_success($response_data);
    } else {
        wp_send_json_error('Database retrieval failed.');
    }
    wp_die();
}


function houzeo_feedback_list_shortcode() {
    ob_start(); 
?>
    <style>
        #custom-contact-form {max-width: 400px;margin: 0 auto;}#custom-contact-form label {display: block;margin-bottom: 5px;}#custom-contact-form input[type="text"], #custom-contact-form input[type="email"], #custom-contact-form input[type="tel"], #custom-contact-form textarea {width: 100%;padding: 10px;margin-bottom: 15px;border: 1px solid #ccc;border-radius: 5px;font-size: 16px;}#custom-contact-form input[type="tel"] {::placeholder {color: #999;}}#custom-contact-form input[type="submit"] {background-color: #0073e6;color: #fff;border: none;border-radius: 5px;padding: 10px 20px;font-size: 18px;cursor: pointer;}#custom-contact-form input[type="submit"]:hover {background-color: #005bbd;}
        table#database-results-table {width: 900px;}
    

    </style>

    <form id="custom-contact-form" action="" method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Phone (US format):</label>
        <input type="tel" name="phone" id="phone" pattern="\(\d{3}\) \d{3}-\d{4}" placeholder="(123) 456-7890" required>

        <label for="comment">Comment:</label>
        <textarea name="comment" id="comment" rows="4" required></textarea>

        <input type="submit" value="Submit">
    </form>


 <?php   
   
    return ob_get_clean(); 
}

add_shortcode('houzeo_feedback_list', 'houzeo_feedback_list_shortcode');


function custom_contact_form_shortcode() {
    ob_start(); 

  ?>  

    <div id="database-data-container"></div>

    <script type="text/javascript">
	jQuery(document).ready(function ($) {
	    $('#custom-contact-form').on('submit', function (e) {
	        e.preventDefault();
	        var form = $(this);
	        var formData = form.serialize();
	        
	        var link = "<?php echo admin_url('admin-ajax.php') ?>";
	        var formAction = 'my_ajax_action'; 
	        var dataFetchAction = 'fetch_database_data'; 

	        jQuery.ajax({
	            type: 'POST',
	            url: link,
	            data: {
	                action: formAction,
	                submit: 'true',
	                name: $('#name').val(),
	                email: $('#email').val(),
	                phone: $('#phone').val(),
	                comment: $('#comment').val()
	            },
	            success: function (response) {
	                if (response.success) {
	                    form[0].reset();
	                    jQuery.ajax({
	                        type: 'POST',
	                        url: link,
	                        data: {
	                            action: dataFetchAction
	                        },
	                        success: function (dataResponse) {
	                            if (dataResponse.success) {
	                                $('#database-data-container').html(dataResponse.data);
	                            }
	                        }
	                    });
	                } 
	            }
	        });
	    });
	});
    </script>
    <?php

    return ob_get_clean(); 
}

add_shortcode('houzeo_feedback_form', 'custom_contact_form_shortcode');

