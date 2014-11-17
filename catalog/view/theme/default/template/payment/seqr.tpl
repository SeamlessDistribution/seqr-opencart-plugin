<div style="margin: auto; max-width: 500px;">
    <link rel="stylesheet" href="https://cdn.seqr.com/webshop-plugin/css/seqrShop.css">

    <script>
        if (window.__seqr_intervalID) window.clearInterval(window.__seqr_intervalID);

        var statusUpdated = function(status) {
            if (status.status === 'PAID') window.location.href = 'index.php?route=checkout/success';
        };
    </script>

    <?php if (isset($qr_code)) { ?>
        <script id="seqrShop"
                src="/seqr/js/seqrShop.js#!<?php echo ($test ? 'mode=demo&' : ''); ?>injectCSS=false&statusCallback=statusUpdated&invoiceQRCode=<?php echo urlencode($qr_code); ?>&statusURL=<?php echo urlencode($url_poll); ?>">
        </script>
    <?php } else
        echo "<h1 style=\"text-align: center;\">{$text_unavailable}</h1>";
    ?>
</div>
