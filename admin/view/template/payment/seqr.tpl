<?php echo $header; ?>
<?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-seqr-uk" data-toggle="tooltip"
                        title="<?php echo $msg_button_save; ?>" class="btn btn-primary">

                    <i class="fa fa-save"></i>
                </button>

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

                    <!-- Terminal ID -->
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="seqr-terminal-id">
                            <?php echo $msg_seqr_terminal_id; ?>
                        </label>

                        <div class="col-sm-10">
                            <input id="seqr-terminal-id" class="form-control" type="text" name="seqr_terminal_id"
                                   value="<?php echo $seqr_terminal_id; ?>"
                                   placeholder="<?php echo $msg_seqr_terminal_id; ?>" />

                            <?php if (isset($error['seqr_terminal_id'])) { ?>
                                <div class="text-danger">
                                    <?php echo $error['seqr_terminal_id']; ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Terminal Password -->
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="seqr-terminal-password">
                            <?php echo $msg_seqr_terminal_password; ?>
                        </label>

                        <div class="col-sm-10">
                            <input id="seqr-password" class="form-control" type="text" name="seqr_terminal_password"
                                   value="<?php echo $seqr_terminal_password; ?>"
                                   placeholder="<?php echo $msg_seqr_terminal_password; ?>" />

                            <?php if (isset($error['seqr_terminal_password'])) { ?>
                                <div class="text-danger"><?php echo $error['seqr_terminal_password']; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- SOAP WSDL URL -->
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="seqr-soap-wsdl-url">
                            <?php echo $msg_seqr_soap_wsdl_url; ?>
                        </label>

                        <div class="col-sm-10">
                            <input id="seqr-soap-wsdl-url" class="form-control" type="text" name="seqr_soap_wsdl_url"
                                   value="<?php echo $seqr_soap_wsdl_url; ?>"
                                   placeholder="<?php echo $msg_seqr_soap_wsdl_url; ?>" />

                            <?php if (isset($error['seqr_soap_wsdl_url'])) { ?>
                                <div class="text-danger"><?php echo $error['seqr_soap_wsdl_url']; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Test/Debug mode -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-live-demo">
                            <?php echo $msg_entry_test; ?>
                        </label>

                        <div class="col-sm-10">
                            <select name="seqr_test" id="input-live-demo" class="form-control">
                                <?php if ($seqr_test) { ?>
                                    <option value="1" selected="selected"><?php echo $msg_text_yes; ?></option>
                                    <option value="0"><?php echo $msg_text_no; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $msg_text_yes; ?></option>
                                    <option value="0" selected="selected"><?php echo $msg_text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Order Status: Paid -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status-paid">
                            <?php echo $msg_seqr_order_status_paid; ?>
                        </label>

                        <div class="col-sm-10">
                            <select name="seqr_order_status_paid" id="input-order-status-paid" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $seqr_order_status_paid) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                                            <?php echo $order_status['name']; ?>
                                        </option>
                                    <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>">
                                            <?php echo $order_status['name']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Order Status: Canceled -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status-canceled">
                            <?php echo $msg_seqr_order_status_canceled; ?>
                        </label>

                        <div class="col-sm-10">
                            <select name="seqr_order_status_canceled" id="input-order-status-canceled" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $seqr_order_status_canceled) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected">
                                            <?php echo $order_status['name']; ?>
                                        </option>
                                    <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>">
                                            <?php echo $order_status['name']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Module disabled/enabled -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status">
                            <?php echo $msg_seqr_status; ?>
                        </label>

                        <div class="col-sm-10">
                            <select name="seqr_status" id="input-status" class="form-control">
                                <?php if ($seqr_status) { ?>
                                    <option value="1" selected="selected"><?php echo $msg_text_enabled; ?></option>
                                    <option value="0"><?php echo $msg_text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $msg_text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $msg_text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>