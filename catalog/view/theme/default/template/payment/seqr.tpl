<div style="margin: auto; max-width: 500px;">
    <link rel="stylesheet" href="https://cdn.seqr.com/webshop-plugin/css/seqrShop.css">

    <script>
        var statusUpdated = function(status) {
            if (status.status === 'PAID')
                window.location.href = 'index.php?route=checkout/success';
        };
    </script>

    <?php if ($qr_code) { ?>
        <script id="seqrShop"
                src="https://cdn.seqr.com/webshop-plugin/js/seqrShop.js#!mode=demo&injectCSS=false&statusCallback=statusUpdated&invoiceQRCode=<?php echo urlencode($qr_code); ?>&statusURL=<?php echo urlencode($url_poll); ?>">
        </script>
    <?php } else
        echo "<h1 style=\"text-align: center;\">{$text_unavailable}</h1>";
    ?>
</div>