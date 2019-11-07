
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Valvoline Libya</title>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--    <link href="css/bootstrap-rtl.css" rel="stylesheet">-->

    <style>
        .strike {
            text-decoration: line-through;
        }

        .bg {
            background:#eee;
            -webkit-transition:background 1s;
            -moz-transition:background 1s;
            -o-transition:background 1s;
            transition:background 1s
        }
    </style>

    <script type="text/javascript">
    
        function preCart(el) {
            if ($(el).val() > 0) {
                $(el).parents(".oilItem").addClass('bg');
            } else {
                $(el).parents(".oilItem").removeClass('bg');
            }
        }

        function resetValue(el) {
            $(el).parents(".oilItem").removeClass('bg');
            $(el).parents(".input-group").find(".quantity").val("0");
        }
        
        
    </script>
    
    
</head>
<body>

<?php
require('excel_reader2.php');
require('SpreadsheetReader.php');
$Reader = new SpreadsheetReader('../items.xlsx');
?>

<div class="container">

    <div class="row text-center">
        <img src="img/logo.png" style="max-width: 400px;">
    </div>

    <div class="row">
        <form class="form-inline" method="post" action="confirm.php">
        <?php
        foreach ($Reader as $key => $row) { if ($key > 0) { ?>
                <div class="col-lg-12 col-sm-12 border oilItem" style="min-height: 70px; border-bottom: 0px; margin: 2px; 0">
                    <div class="row">

                        <div class="col-lg-1 p-1">
                            <?php if (!empty($row[9])) { ?>
                                <img style="max-height: 70px;" src="img/<?php echo $row[9]; ?>"
                                     alt="<?php echo $row[1]; ?>">
                            <?php } ?>
                        </div>
                        <div class="col-lg-6 p-2 pt-3">
                            <h3 class="h6 text-info"><?php echo $row[0]; ?> - <?php echo $row[1]; ?></h3>
                            <?php if (!empty($row[9])) { ?>
                                <small class="text-muted"><?php echo $row[8]; ?></small><?php } ?>
                        </div>
                        <div class="col-lg-1 p-2 pt-4 text-center">
                            <p class="text-info"><?php echo $row[2]; ?></p>
<!--                            <p class="small">--><?php //echo $row[3]; ?><!--</p>-->
                        </div>
                        <div class="col-lg-1 p-2 pt-4 text-center" style="direction: rtl; border-left: 1px solid #ccc;">
                            <span class="text-danger font-weight-bold"><?php if (!empty($row[6])) { ?>د.ل <?php echo $row[6]; ?><?php } ?></span>
                            <p class="<?php if (!empty($row[6])) { echo "strike small"; } ?> text-muted">د.ل <?php echo $row[5]; ?></p>
                        </div>

                        <div class="col-lg-3 pt-3 text-center quantityBox" style="direction: rtl; border-left: 1px solid #ccc;">
                            <?php if ( $row[7] == 1 ) { ?>
                                <div class="form-group row p-2">

                                    <div class="input-group col-lg-8" style="direction: ltr">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-outline-secondary" type="button" onclick="resetValue(this)">x</button>
                                        </div>
                                        <input type="number" min="0" max="<?php echo $row[4]; ?>" onchange="preCart(this)" class="form-control quantity" id="quant<?php echo $row[0]; ?>" name="<?php echo $row[1]; ?>" placeholder="0">
                                    </div>
                                    <label for="quant<?php echo $row[0]; ?>" class="text-muted col-form-label col-lg-4"><?php echo $row[3]; ?></label>
                                </div>
                            <? } else { ?>
                                <p class="text-muted p-2 pt-3">غير متوفر</p>
                            <? } ?>
                        </div>



                        </div>
                </div>
            <?php }} ?>
        </form>
    </div>
</div>

</body>