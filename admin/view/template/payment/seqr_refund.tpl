<?php echo $header; ?>
<?php echo $column_left; ?>


<style type="text/css">
    {literal}
    .table {
        display: table;
        width: 100%;
    }

    .table > * {
        display: table-row;

    }

    .table > *.table-header {
        font-weight: bold;
    }

    .table > * > * {
        display: table-cell;
        padding: 5px 0;
    }

    {/literal}
</style>
<script type="text/javascript">
    window.refund = {
        orderId: null,

        submitRefund: function() {
            $('#' + this.orderId + "_form").submit();
        },

        selectRefund: function(orderId) {
            this.orderId = orderId;
            var amount = $('#' + this.orderId + '_to_return').val();
            if(confirm("Do you want to return " + amount + " to the customer?")) {
                this.submitRefund();
            }
        }
    };
</script>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip"
                   title="<?php echo $msg_button_cancel; ?>" class="btn btn-default">

                    <i class="fa fa-reply"></i>
                </a>
            </div>

            <h1><?php echo $msg_heading_title; ?></h1>

            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li>
                        <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if (isset($error['error_warning'])) { ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $msg_text_edit; ?></h3>
            </div>

            <div class="panel-body">
                <form  id="form-seqr-uk" class="form-horizontal" action="<?php echo $action; ?>"
                       method="post" enctype="multipart/form-data">

          		           <?php print_r($data['seqr_order']) ?>
                </form>

                <div class="panel">
                    <div class="table">
                        <div class="table-header">
                            <div>Id</div>
                            <div>Customer Name</div>
                            <div>Order value</div>
                            <div>Shipping cost</div>
                            <div>Returned</div>
                            <div>Return value</div>
                            <div>Action</div>

                        </div>
                        <?php foreach ($data['seqr_order'] as $row) { ?>
                            <form method="post" id="<?php echo $row['order_id'] . '_form' ?>" action="">
                                <div>
                                    <a href="<?php echo $row['order_id'] ?>"><?php echo $row['order_id'] ?></a>
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id'] ?>"/>
                                </div>
                                <div id="<?php echo $row['order_id'] . '_customer_name' ?>"><?php echo $row['customerName'] ?></div>
                                <div id="<?php echo $row['order_id'] . '_total' ?>"><?php echo $row['total'] ?></div>
                                <div id="<?php echo $row['order_id'] . 'shipping' ?>"><?php echo $row['refund'] ?></div>
                                <div id="<?php echo $row['order_id'] . '_returned' ?>"><?php echo $row['refund'] ?></div>
                                <div>
                                    <?php if($row['total'] - $row['refund'] == 0) { ?>
                                    	Fully refunded
                                    <?php } else { ?>
                                    	<input id="<?php echo $row['order_id'] . '_to_return' ?>" name="return" type="number" step="0.01" min="0"
                                           max="<?php echo ($row['total'] - $row['refund']) ?>" value="<?php echo $row['suggested_return'] ?>"/>
                                    <?php } ?>
                                </div>
                                <div>
                                    <?php if ($row['total'] - $row['refund'] != 0) { ?>
                                        <input class="button btn btn-default button-medium" type="button" value="Refund" onclick="window.refund.selectRefund(<?php echo $row['order_id'] ?>)" />
                                    <?php } ?>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
