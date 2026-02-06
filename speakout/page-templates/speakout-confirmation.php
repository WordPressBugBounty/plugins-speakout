<?php error_reporting(0); ini_set('display_errors', 0); ?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_bloginfo( 'charset' ); ?>" />
    <meta http-equiv="refresh" content="5;url=<?php echo esc_url( $returnURL ); ?>">
    <title><?php echo get_bloginfo( 'name' ); ?></title>
    <style type="text/css">
        body {
            background: #666;
            font-family: arial, sans-serif;
            font-size: 14px;
        }
        #confirmation {
            background: #fff url('<?php echo plugins_url( 'speakout' ); ?>/images/mail-stripes.png') repeat top left;
            border: 1px solid #fff;
            width: 515px;
            margin: 200px auto 0 auto;
            box-shadow: 0px 3px 5px #333;
        }
        #confirmation-content {
            background: #fff url('<?php echo plugins_url( 'speakout' ); ?>/images/postmark.png') no-repeat top right;
            margin: 10px;
            padding: 40px 0 20px 100px;
        }
    </style>
</head>
<body>
    <div id="confirmation">
        <div id="confirmation-content">
            <h2><?php _e( 'Email Confirmation', 'speakout' ); ?></h2>
            <p><?php echo $message; ?></p>
            <p><?php _e( "If you aren't redirected", 'speakout' ); ?> <a href="<?php echo esc_url( $returnURL ); ?>"><?php _e( 'Click here', 'speakout' ); ?></a> <?php _e( 'to return to site.', 'speakout' ); ?></p>
        </div>
    </div>
</body>
</html>