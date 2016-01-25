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

          		 <?php print_r($data['seqr_order']) ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>