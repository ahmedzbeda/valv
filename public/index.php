<pre>
<?php
require('excel_reader2.php');
require('SpreadsheetReader.php');
$Reader = new SpreadsheetReader('../items.xlsx');
?>
</pre>
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
    <link href="js/jquery-ui.min.css" rel="stylesheet">

    <style>
        .strike {
            text-decoration: line-through;
        }
    </style>

</head>
<body>

<div class="container">
    <div class="row">
        <?php
        foreach ($Reader as $key => $row) { if ($key > 0) { ?>
                <div class="col-lg-12 col-sm-12 border" style="min-height: 70px;">
                    <div class="row">

                        <div class="col-lg-1 p-1">
                            <?php if (!empty($row[9])) { ?>
                                <img style="max-height: 70px;" src="img/<?php echo $row[9]; ?>"
                                     alt="<?php echo $row[1]; ?>">
                            <?php } ?>
                        </div>
                        <div class="col-lg-6 p-2">
                            <h3 class="h6 text-info"><?php echo $row[0]; ?> - <?php echo $row[1]; ?></h3>
                            <?php if (!empty($row[9])) { ?>
                                <small class="text-muted"><?php echo $row[9]; ?></small><?php } ?>
                        </div>
                        <div class="col-lg-1 p-2 text-center">
                            <p class="text-danger"><?php echo $row[2]; ?></p>
                            <p class="small"><?php echo $row[3]; ?></p>
                        </div>
                        <div class="col-lg-1 p-2 text-center">
                            <p class="text-danger font-weight-bold"><?php if (!empty($row[6])) { ?>د.ل <?php echo $row[6]; ?><?php } ?></p>
                            <p class="<?php if (!empty($row[6])) { echo "strike small"; } ?>">د.ل <?php echo $row[5]; ?></p>
                        </div>

                    </div>
                </div>
            <?php }} ?>

    </div>
</div>

</body>