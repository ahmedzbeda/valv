<?php session_start();
//header('Content-Type: text/plain'); ?>

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
    <link href="css/bootstrap-rtl.css" rel="stylesheet">

    <style>

        @import url('https://fonts.googleapis.com/css?family=Tajawal&display=swap');

        * {
            font-family: 'Tajawal', sans-serif;
        }

        .strike {
            text-decoration: line-through;
        }

        .bg {
            background: #fafafa;
            border: 2px solid greenyellow !important;
            -webkit-transition: background 1s;
            -moz-transition: background 1s;
            -o-transition: background 1s;
            transition: background 1s

        }

        .disableClick{
            pointer-events: none;
            background: #666;
        }
    </style>

    <script type="text/javascript">
        function itemsSum() {
            var total = 0;
            $('input[type="number"]').each(function (i, obj) {
                // console.log(jQuery.type( $(obj).val() ));

                var a = parseInt($(obj).val());
                a = a ? a : 0;
                var n = a * parseInt($(obj).attr('price'));
                total += n;
            });
            $('.total').html(total);

            if (total > 0) {
                $('.invoice').removeClass('disableClick');
            } else {
                $('.invoice').addClass('disableClick');

            }
        }

        function preCart(el) {
            if ($(el).val() > 0) {
                $(el).parents(".oilItem").addClass('bg');
            } else {
                $(el).parents(".oilItem").removeClass('bg');
            }
            itemsSum();

        }

        function resetValue(el) {
            $(el).parents(".oilItem").removeClass('bg');
            $(el).parents(".input-group").find(".quantity").val("0");
            itemsSum();
        }

        $(document).ready(function () {
            itemsSum();
            $('input[type="number"]').each(function (i, obj) {
                    if ($(obj).val() > 0) {
                        $(obj).parents(".oilItem").addClass('bg');
                    } else {
                        $(obj).parents(".oilItem").removeClass('bg');
                    }
                }
            );
        })
    </script>
</head>
<body style="padding-bottom: 200px;">

<?php if (!empty($_SESSION['username'])) { ?>
    <nav class="navbar navbar-dark bg-dark d-flex align-items-end bd-highlight">
        <h3 class="text-white m-0"><?php echo $_SESSION['username']; ?></h3>
        <form class="form-inline" method="post" action="index.php">
            <button name="submit" value="logout" class="btn btn-danger">خروج</button>
        </form>
    </nav>
<?php } ?>


<div class="container">
    <div class="row pt-5 pb-4">
        <div class="col-xs-6 text-right">
            <a href="index.php"><img src="img/logo2.png" style="max-width: 100%;"></a>
        </div>
        <div class="col-xs-6 text-left">
            <a href="index.php"><img src="img/logo.png" style="max-width: 100%;"></a>
        </div>
    </div>

    <?php
    require('excel_reader2.php');
    require('SpreadsheetReader.php');
    $reader = new SpreadsheetReader('../items.xlsx');
    $customers = new SpreadsheetReader('../customers.xlsx');

    if (empty($_POST)) {

        if (empty($_SESSION['username'])) {
            showLogin(true);
        } else {
            listItems();
        }

    } else {
        if ($_POST['submit'] == 'cart') {
            $s = base64_encode(serialize($_POST));
            showInvoice();

        } elseif ($_POST['submit'] == 'edit') {
            $s = unserialize(base64_decode($_POST['serial']));
            listItems();

        } elseif ($_POST['submit'] == 'email') {

            $items = unserialize(base64_decode($_POST['serial']));

            $message = "<p>Invoice: " . $items['timestamp'] . "<br />";
            $message .= $_SESSION['id'] . " - " . $_SESSION['username'];
            $message .= "<br />" . $_SESSION['address'] . "<br />" . $_SESSION['phone'] . "</p>";
            $message .= "<table width='100%' border='1'>";
            $total = 0;
            foreach ($items['items'] as $item => $quantity) {
                foreach ($reader as $areader) {
                    if ($item == $areader[0]) {
                        if ($quantity != 0) {
                            $price = empty($areader[6]) ? $areader[5] : $areader[6];
                            $itemTotal = $price * $quantity;
                            $total += $itemTotal;
                            $message .= "<tr>";
                            $message .= "<td align='left'>" . $item . " " . $areader[1] . " " . $areader[2] . "</td>";
                            $message .= "<td align='center'>" . $quantity . "</td><td align='center'>" . $price . "</td><td align='center'>" . $itemTotal . "</td>";
                            $message .= "</tr>";
                        }
                    }
                }
            }
            $message .= "<tr><td colspan='4' align='right'>Total: " . $total . "</td></tr></table>";


            $to = '0@0.ly';
            $subject = 'New Invoice ' . $items['timestamp'];
            $headers = 'From: order@arwad.ly' . "\r\n" .
                'Reply-To: order@arwad.ly' . "\r\n" .
                'Content-Type: text/html; charset=UTF-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);

            showConfirm();

        } elseif ($_POST['submit'] == 'login') {

            foreach ($customers as $customer) {
                if ($customer[4] == $_POST['username']) {
                    $_SESSION['username'] = $customer[1];
                    $_SESSION['address'] = $customer[2];
                    $_SESSION['phone'] = $customer[3];
                    $_SESSION['id'] = $customer[4];
                    header("Refresh:0");
                    exit;
                }
            }
            showLogin(false);

        } elseif ($_POST['submit'] == 'logout') {
            session_destroy();
            header("Refresh:0");
        }
    }

    ?>



    <?php function listItems()
    {
        global $reader, $s; ?>
        <div class="row">
            <form class="form-inline" method="post" autocomplete="off" action="index.php">
                <?php
                foreach ($reader as $key => $row) {
                    if ($key > 0) { ?>
                        <div class="col-lg-12 col-sm-12 border oilItem"
                             style="min-height: 70px; border-bottom: 0px; margin: 2px; 0">
                            <div class="row">
                                <div class="col-lg-1 col-xs-2 p-1">
                                    <?php if (!empty($row[9])) { ?>
                                        <img class="pt-2" style="max-height: 70px;" src="img/<?php echo $row[9]; ?>"
                                             alt="<?php echo $row[1]; ?>">
                                    <?php } ?>
                                </div>
                                <div class="col-lg-5 col-xs-6 p-2 pt-3">
                                    <h3 class="h6 text-info"><?php echo $row[0]; ?> - <?php echo $row[1]; ?></h3>
                                    <?php if (!empty($row[9])) { ?>
                                        <small class="text-muted"><?php echo $row[8]; ?></small><?php } ?>
                                </div>
                                <div class="col-lg-1 col-xs-1 p-2 pt-3 text-center">
                                    <p class="text-info"><?php echo $row[2]; ?></p>
                                </div>
                                <div class="col-lg-2 p-2 col-xs-3 pt-3 text-center"
                                     style="direction: rtl; border-left: 1px solid #ccc;">
                                    <span class="text-danger font-weight-bold"><?php if (!empty($row[6])) { ?><?php echo $row[6]; ?> د.ل<?php } ?></span>
                                    <p class="<?php if (!empty($row[6])) {
                                        echo "strike small";
                                    } ?> text-muted"><?php echo $row[5]; ?> د.ل</p>
                                </div>

                                <div class="col-lg-3 col-xs-12 pt-1 text-right quantityBox"
                                     style="direction: rtl; border-left: 1px solid #ccc;">
                                    <?php if ($row[7] == 1) { ?>
                                        <div class="form-group row p-lg-2" style="margin-bottom: 5px !important;">
                                            <div class="input-group col-lg-8 col-xs-8" style="direction: ltr">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                            onclick="resetValue(this)">x
                                                    </button>
                                                </div>
                                                <input type="number" min="0" max="<?php echo $row[4]; ?>"
                                                       price="<?php echo empty($row[6]) ? $row[5] : $row[6]; ?>"
                                                       onchange="preCart(this)" onkeyup="preCart(this)"
                                                       class="form-control quantity" id="quant<?php echo $row[0]; ?>"
                                                       name="items[<?php echo $row[0]; ?>]"
                                                       oninvalid="this.setCustomValidity('يجب أن يكون رقم وأن لا يتجاوز أقصى كمية')"
                                                       oninput="this.setCustomValidity('')"
                                                       value="<?php if ($s['items'][$row[0]] != 0) echo $s['items'][$row[0]]; else echo 0; ?>">
                                                <input type="hidden" name="timestamp" value="<?php echo time() ?>">

                                            </div>
                                            <label for="quant<?php echo $row[0]; ?>"
                                                   class="text-muted col-form-label col-lg-4 col-xs-4"><?php echo $row[3]; ?></label>
                                        </div>
                                        <p>
                                            <small class="text-right pr-3" style="color: #999;">أقصى
                                                كمية <?php echo $row[4]; ?> <?php echo $row[3]; ?></small>
                                        </p>
                                    <?php } else { ?>
                                        <p class="text-muted p-2 pt-3">غير متوفر</p>
                                    <?php } ?>

                                </div>

                            </div>
                        </div>
                    <?php }
                } ?>
                <div class="fixed-bottom bg-dark border-top pt-3">
                    <div class="container">
                        <button class="invoice float-left btn btn-light mx-3 disableClick" name="submit" value="cart">إنشاء الفاتورة</button>
                        <h1 class="float-right text-white"><span class="total">0</span> د.ل </h1>
                    </div>
                </div>
            </form>
        </div>
    <?php } ?>

    <?php function showInvoice()
    {
        global $reader, $_POST, $s; ?>
        <p class="h5">فاتورة: <?php echo $_POST['timestamp']; ?></p>
        <h3>لصالح: <?php echo $_SESSION['username']; ?></h3>
        <p><?php echo $_SESSION['address']; ?></p>
        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-center">رقم الصنف</th>
                    <th class="text-right">اسم الصنف</th>

                    <th class="text-right">السعر</th>
                    <th class="text-center">الكمية</th>
                    <th class="text-left">الإجمالي</th>
                </tr>
                </thead>
                <tbody>

                <?php $total = 0;
                foreach ($reader

                as $key => $row) { ?>
                <?php foreach ($_POST['items']

                as $inputKey => $input) { ?>
                <?php if ($row[0] == $inputKey && $input != 0) { ?>

                <tr>
                    <td class="text-center"><?php echo $row[0] ?></td>
                    <td class="text-right strong"><?php echo $row[2] ?> - <?php echo $row[1] ?> </td>

                    <td class="text-right"><?php echo empty($row[6]) ? $row[5] : $row[6] ?></td>
                    <td class="text-center"><?php echo $input ?></td>
                    <td class="text-left" style="white-space: nowrap"><?php $p = empty($row[6]) ? $row[5] : $row[6];
                        echo $p * $input ?> د.ل
                    </td>

                    <?php $total += $p * $input;
                    }
                    }
                    } ?>

                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" class="text-right align-middle"><strong>الإجمالي:</strong></td>
                    <td colspan="2" class="text-left align-middle"><h3 class="font-weight-bold"><?php echo $total; ?> د</h3></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-left align-middle px-0" style="background: #FFF;">
                        <form class="form" method="post" action="index.php">
                            <input type="hidden" value="<?php print $s; ?>" name="serial">
                            <button name="submit" value="edit" class="btn btn-danger">تعديل</button>
                            <button name="submit" value="email" class="btn btn-success">إرسال الفاتورة</button>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php } ?>


    <?php function showConfirm()
    { ?>
        <h1 class="text-center text-dark pt-5">لقد تم إرسال طلبك، شكرًا لاختياركم فالفولين</h1>
        <h4 class="text-center text-muted">لأي استفسار يرجى الاتصال على الأرقام 0917050555 - 0927050555</h4>

    <?php } ?>

    <?php function showLogin($status = true)
    { ?>
        <div class="container h-80 pt-5">
            <div class="row align-items-center h-100">
                <div class="col-lg-3 col-md-6 col-xs-10 mx-auto">
                    <div class="text-center">
                        <p id="profile-name" class="profile-name-card text-danger"><?php if ($status == false) {
                                echo "إدخال غير صحيح !";
                            } ?></p>
                        <form class="form-signin" method="post" action="index.php">
                            <input type="text" name="username" id="input" class="form-control form-group"
                                   placeholder="رقم الهاتف" required autofocus>
                            <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit" name="submit"
                                    value="login">دخول
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <p class="text-muted font-weight-bold text-center text-sm-left mt-5">شركة أرواد للزيوت. الوكيل الرسمي لشركة فالفولين
        في ليبيا
        <br/>شارع البرج، سوق الجمعة، طرابلس
        <br/>0917050555 - 0927050555</p>
</div>

<!-- Google Analytics -->


</body>