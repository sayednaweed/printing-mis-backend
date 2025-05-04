<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <style>
        .page {
            page-break-after: avoid;
            /* Add a page break after each pair of student cards */
        }

        .card {
            width: 100%;
            /* border: 1px solid black; */
            /* border-radius: 15px; */
            overflow: hidden;
            padding-bottom: 20px;
            margin-bottom: 100px;
            top: 0;
            padding-top: -5%;
            /* page-break-inside: avoid; */
            /* page-break-inside: avoid; */
        }

        /* @media print {
      .card {
        page-break-inside: avoid;
      }
    } */
        .info {
            width: 100%;
            align-items: center;
            /* border:1px solid red; */
            grid: auto;
        }

        img.islamic_logo {
            width: 100px;
            height: 100px;
            float: right;
            margin-top: 0px;
            margin-right: -20px
        }

        img.moph_logo {
            width: 108px;
            height: 100px;
            float: left;
            margin-top: 0px;
            margin-left: -20px;
        }

        div.mintext {
            /* position: ; */
            text-align: center;
            float: left;
            width: auto;
            margin: 0;
            padding-top: -1.5%;
            margin-left: auto;
            /* margin-top:10px; */
            font-weight: bold;
            font-size: 1rem;
        }

        div.mintextKey {
            /* position: ; */
            text-align: center;
            float: left;
            width: auto;
            margin: 0;
            margin-top: 0px;
            margin-left: auto;
            /* margin-top:10px; */
            font-size: 0.9rem;
        }

        .detials {
            width: 100%;
            text-align: center;
        }

        .title {
            float: right;
            /* margin-left:40px;  */
            text-align: right;
        }

        .detail_value {
            float: right;
            text-align: center;
        }

        .contents {
            float: right;
            right: 0;
            padding-right: -40px;
            margin-left: 200px;
        }

        table.table {
            width: 95%;
            text-align: left;
            /* border: 2px solid black; */
            padding: 0;
            margin-left: auto;
            margin-right: auto;
            margin-top: 10px;
        }

        table.table tr td {
            border: 1px solid black;
            margin: 0;
            font-size: 1rem;
        }

        table.table tr th {
            border: 1px solid black;
            margin: 0;
            font-size: 1rem;
            text-align: left;
        }

        .bottomdiv {
            height: 30%;
            border: 1px solid black;
            width: 150%;
            padding: 0px;
            margin: 0px;
        }
    </style>

    <body>

        <div class="page">

            <div class="card">

                <div class="info">

                    <img src="images/islamic.png" alt="img" class="islamic_logo" style="">
                    <img src="images/moph.png" alt="" class="moph_logo" style="">
                    <div class="mintext">
                        امــارت اســــلامـی افـغـانـــســتـان
                        <br>
                        وزارت صــــحـــت عـــامـــه
                        <br>
                        ریــاست مـالـی و حســابــی
                        <br>
                        رســیــد کــارت واکــسـیـن

                        <hr>

                        <div class="detials">

                            <div class="title" dir="rtl">
                                <div style="margin-right:40px; font-weight: normal;">
                                    تاریخ صدور : {{ now() }} <span
                                        style="color:white; ">____________________________________________________________________</span>
                                    نمبر تعرفه : {{ $data['payment_no'] }}
                                </div>

                                <hr>

                                <br>
                                <table width="100%"
                                    style="height: 100%;  margin-right:40px; font-size: 1rem; font-weight: bold;">

                                    <tr>

                                        <!-- Bottom left: Contact Info -->
                                        <td valign="bottom"
                                            style="width: 50%; margin-top:  20px; text-align: right; padding-bottom: 10px; ">
                                            نام : {{ $data['full_name'] }}
                                            <br>
                                            <br>

                                            نوع مسافر : {{ $data['travel_type'] }}
                                        </td>

                                        <!-- Bottom right: QR Code -->
                                        <td align="right" valign="bottom" style="width: 50%;">
                                            شماره پاسپورت: {{ $data['passport_number'] }}
                                            <br>
                                            <br>

                                            مبلغ پرداخت : {{ $data['paid_amount'] }} افغانی

                                        </td>
                                    </tr>
                                </table>

                                <hr>
                                <br>

                                <table width="100%" style="height: 100%; margin-bottom:-100px; margin-right:40px;">

                                    <tr>

                                        <!-- Bottom left: Contact Info -->
                                        <td valign="bottom"
                                            style="width: 50%; margin-top:  20px; text-align: right; padding-bottom: 10px; ">
                                            نام اخذ کننده : {{ $data['user_name'] }}
                                            <br>
                                            <br>
                                            <br>
                                            محل مربوطه : {{ $data['zone'] }}

                                            <br>
                                            <br>
                                            <br>

                                            <br>
                                            <br>

                                            <br>
                                            <br>
                                            امضای اخذ کننده : .................................
                                        </td>

                                        <!-- Bottom right: QR Code -->
                                        <td align="right" valign="bottom" style="width: 50%;">
                                            نمبر راجستر اخذ کننده : {{ $data['registeration_number'] }}
                                            <br>
                                            <br>
                                            <br>

                                            <br>
                                            <br>
                                            <br>

                                            <br>
                                            <br>

                                            <br>
                                            <br>

                                            امضای اخذ مشتری : .................................

                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        </div>
        </div>

    </body>

</html>
