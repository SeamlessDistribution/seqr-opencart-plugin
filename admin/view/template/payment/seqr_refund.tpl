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


                </form>

                <div class="panel">
                    <div class="panel-heading">
                        SEQR Payments
                    </div>
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
                        <?php foreach ($seqrPayments as $row) { ?>
                            <form method="post" id="{$row['id_order']}_form" action="{$link->getAdminLink('SeqrRefunds',true)|escape:'html':'UTF-8'}">
                                <div>
                                    <a href="<?php $row['order_link'] ?>"><?php $row['id_order'] ?></a>
                                    <input type="hidden" name="id_order" value="{$row['id_order']}"/>
                                </div>
                                <div id="{$row['id_order']}_customer_name"><?php $row['customerName'] ?></div>
                                <div id="{$row['id_order']}_total"><?php $row['total_paid'] ?></div>
                                <div id="{$row['id_order']}_shipping"><?php $row['shipping_cost'] ?></div>
                                <div id="{$row['id_order']}_returned"><?php $row['returned'] ?></div>
                                <div>
                                    <?php if($row['total_paid'] - $row['returned'] == 0) { ?>
                                    	Fully refunded
                                    <?php } else { ?>
                                    	<input id="{$row['id_order']}_to_return" name="return" type="number" step="0.01" min="0"
                                           max="{$row['total_paid'] - $row['returned']}" value="{$row['suggested_return']}"/>
                                    <?php } ?>
                                </div>
                                <div>
                                    <?php if ($row['total_paid'] - $row['returned'] != 0) { ?>
                                        <input class="button btn btn-default button-medium" type="button" value="Refund" onclick="window.refund.selectRefund({$row['id_order']})" />
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
