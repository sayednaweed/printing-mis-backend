<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/employee/contract.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>

    <body>

        <div class="page-break">

            <div class="card">

                <div class="info">

                    <img src="images/app-logo.png" alt="" class="app_logo" style="">
                    <img src="images/waheed.jpg" alt="" class="employee_photo" style="">
                    <div class="mintext">
                        {{ $data['company_name'] }}
                        <br>
                        مدیریت منابع بشری

                        <div class="detials">

                            <div class="title" dir="rtl">
                                <div style="margin-right:5px; font-weight: normal; font-size:11px;">

                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>

                                    فورم قرار داد کاری کارمندان
                                    <span
                                        style="color:white; ">____________________________________________________________________</span>
                                    نمبر مسلسله :
                                    {{ $data['hr_code'] }}
                                </div>

                                <hr style="width:100%">
                            </div>

                            <br>
                            <table dir="rtl" width="100%"
                                style="border: 1px solid black; border-collapse: collapse; text-align: center; font-size: 12px;">

                                <tr>
                                    <td class="gray-400" colspan="11"
                                        style="border: 1px solid black; font-weight: bold;">شهرت قرارداد
                                        کننده</td>
                                </tr>
                                <tr class="gray-300">
                                    <td colspan="2" class="border">شهرت</td>
                                    <td colspan="3" class="border">تاریخ تولد</td>
                                    <td colspan="2" class="border">جنسیت/حالت مدنی</td>
                                    <td colspan="4" class="border">مشخصات تذکره تابعیت</td>
                                </tr>
                                <tr class="gray-300">
                                    <th class="border">اسم</th>
                                    <td class="border">ولد / بنت</td>
                                    <td class="border">روز</td>
                                    <td class="border">ماه</td>
                                    <td class="border">سال</td>
                                    <td class="border">جنسیت</td>
                                    <td class="border">حالت مدنی</td>
                                    <td class="border">نمبر</td>
                                    <td class="border">ثبت</td>
                                    <td class="border">صفحه</td>
                                    <td style="border: 1px solid black; max-width:50px">جلد</td>
                                </tr>
                                <tr>
                                    <th class="border">{{ $data['full_name'] }}</th>
                                    <td class="border"> {{ $data['f_name'] }}</td>
                                    <td class="border">{{ $data['birth_day'] }}</td>
                                    <td class="border">{{ $data['birth_month'] }}</td>
                                    <td class="border">{{ $data['birth_year'] }}</td>
                                    <td class="border">{{ $data['gender'] }}</td>
                                    <td class="border">{{ $data['marital_status'] }}</td>
                                    <td class="border">{{ $data['nid_no'] }}</td>
                                    <td class="border">{{ $data['regestration'] }}</td>
                                    <td class="border">{{ $data['page'] }}</td>
                                    <td class="border">{{ $data['volume'] }}</td>
                                </tr>

                                <tr>
                                    <td colspan="1" class="border gray-300">سکونت</td>
                                    <td colspan="1" class="border gray-300">اصلی</td>
                                    <td colspan="4" class="border"> {{ $data['org_province'] }}
                                    </td>
                                    <td colspan="1" class="border gray-300">فعلی</td>
                                    <td colspan="4" class="border">{{ $data['cur_province'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="border gray-300">درجه تحصیل </td>
                                    <td colspan="3" class="border gray-300">بخش مربوطه</td>
                                    <td colspan="3" class="border gray-300">بست</td>
                                    <td colspan="2" class="border gray-300">شفت کاری</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="border">{{ $data['education'] }}</td>
                                    <td colspan="3" class="border">{{ $data['department'] }}</td>
                                    <td colspan="3" class="border">{{ $data['position'] }}</td>
                                    <td colspan="2" class="border">{{ $data['shift'] }} </td>
                                </tr>
                                <tr>
                                    <td colspan="5" rowspan="2" class="border gray-300">نام و امضاء تکمیل
                                        کننده
                                        فورم:
                                    </td>
                                    <td colspan="6" style="text-align:right; border: 1px 1px 1px 0px solid black;">
                                        {{ $data['full_name'] }} :
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="6"
                                        style="text-align:right; border:1px 1px 1px 0px solid black; color:white; opacity: 1;">
                                        <br>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="11" class=" gray-400  border"style="font-weight: bold;">

                                        شرح مختصر نوعیت استخدام
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="border"
                                        style="text-align:right;  color:white; opacity: 1;">

                                        <br>
                                        <br><br>
                                        <br><br>
                                        <br>
                                        <br>
                                    </td>
                                </tr>

                            </table>

                            <table dir="rtl" width="100%"
                                style="border: 1px solid black; border-collapse: collapse; text-align: center; font-size: 12px;">

                                <tr>
                                    <td class="gray-300 border" colspan="2" style="font-weight: bold;"> تاریخ شروع
                                        قرارداد:
                                    </td>
                                    <td class=" border" colspan="3"> تاریخ شروع
                                        قرارداد:
                                    </td>
                                    <td class="gray-300 border" colspan="3" style="font-weight: bold;"> تاریخ شروع
                                        قرارداد:
                                    </td>
                                    <td class="border" colspan="3"> تاریخ شروع
                                        قرارداد:
                                    </td>
                                </tr>
                                <tr class="gray-300">
                                    <td colspan="11" class="border">معاش</td>

                                </tr>
                                <tr class="">
                                    <th colspan="2" class="border gray-300">معاش ماهوار</th>
                                    <td colspan="2" class="border">4000</td>
                                    <td colspan="2" class="border gray-300">اضافه کاری فی ساعت</td>
                                    <td colspan="2" class="border">100</td>
                                    <td colspan="2" class="border gray-300">واحد ارز</td>
                                    <td colspan="1" class="border">افغانی</td>

                                </tr>

                            </table>

                            <br>
                            <br>
                            <br>

                            <table width="100%" dir="rtl"
                                style="height: 100%; font-size:12px; margin-bottom:-100px; margin-right:0px;">

                                <tr>
                                    <td colspan="12">
                                        <br>
                                    </td>

                                </tr>
                                <tr>
                                    <td colspan="12">
                                        تعهد: طرفین تعهد میسپاریم که تمام ماده ها این قرار داد را به دقت مرور نموده و
                                        طبق آن عمل مینمایم همچنان پابند به موارد آن میباشیم.
                                    </td>
                                </tr>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <tr>
                                    <td colspan="6">
                                        امضاء و تاریخ قرار داد کننده:
                                    </td>
                                    <td colspan="6">
                                        امضاء و تاریخ قرار داد کننده:
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

        <div class="page " dir="rtl" style="font-size:12px; ">
            <b>
                ماده اول: شرح وظایف و مسولیت های کارمند

            </b>
            <br>
            کارمند موظف است تا در ساعت های کاری مشخص شده در لایحه وظایف ( از سوی کارفرما )، مسولیت ها مشخص شده خویش را
            انجام دهد.
            <br>
            ساعات کاری کارمند [۱۰] ساعت بوده که شروع آن از ساعت ۵ صبح میباشد وختم آن ساعت ۳ عصر میباشد در صورت نیاز و
            ضرورت قابل تغییر میباشد.
            <br>

            کارمند در قبال حفظ و نگهداری وسایل و ماشین آلات مطبعه مسول بوده و در صورت سهل انگاری با برخورد قانونی مواجع
            خواهد شد.
            <br>
            کارمند حین اجراء کار باید دستورات ایمنی و حیاتی را در نظر داشته باشد در صورت بی توجه یی مسولیت بدوش خود فرد
            خواهد بود.
            <br>
            کارمند مسول میباشد در صورت غیر حاضری حد اقل یک روز قبل اداره را در جریان گذاشته و فورم رخصتی اخذ نماید.

            <br>
            کارمند موظف است تا طبق هدایت ریس و مدیران مطبعه فعالیت نمایید و از گفته هایشان در چهارچوب اصول اطاعت
            نمایند.
            <br>
            <b>
                ماده دوم: مدت قرارداد

            </b>
            <br>

            این قرار داد به مدت یک سال میباشد در صورت توافق طرفین تمدید این قرار داد امکان پذیر است.
            <br>
            فسخ قرار داد در صورت برخوردن یکی از موارد به ماده پنجم این قرار داد، قبل از تکمیل معیاد آن امکان پذیر
            میباشد.
            <br>
            <b>
                ماده سوم: حقوق، امتیازات و مزایا

            </b>
            <br>

            کارفرما موظف است تا ماهانه مبلغ که قبلا در قرار داد توافق صورت گرفته به کارمند (دارای/ قرارداد) منحیث حقوق
            ماهانه پرداخت نماید.
            <br>

            کارمند در صورت انجام کار بعد از ساعات کاری (در صورت تمایل) مستحق اضافه کاری شمرده میشود.
            <br>
            کارمند در مدت یک سال کاری ۲۰ روز رخصتی حق دارد که ۱۰ روز آن مریضی میباشد و ۱۰ روز آن تفریحی (در صورت اخذ
            بیش از ۳ روز رخصتی مریضی تصدیق داکتر نیاز میباشد).
            <br>
            <b>
                ماده چهارم: رسیدگی به اختلافات

            </b>
            <br>
            صورتیکه کارمند با اعضاء مطبعه اختلافی داشته باشد موضوع را باید از طریق اداره (کارفرما، مدیریت یا دفتر )
            با مذاکره و گفتگو حل و فصل نماید.
            <br>
            در صورت عدم دستیابی به توافق از سوی اداره (کارفرما/مدیریت) اختلاف به کمیته ویا هئیت داوری ارجاع خواهد یافت
            و هئیت داوری از سوی اداره (از جمله مدیران) تعیین میگردد.
            <br>
            تصمیمات داوری قطعی و لازم الاجرا خواهد بود و طرفین موظف به اجراء آن میباشد.
            <br>
            در صورت هر نوع منازعه که طبق بند های ماده چهارم بین جانبین حل و فصل نگردید، با در نظرداشت قوانین دولت موضوع
            به مراجع حقوقی قانونی راجع خواهد گردید.
            <br>
            <b>
                ماده پنجم: شرایط فسخ قرارداد

            </b>
            <br>
            این قرار داد در شرایط ذیل قابل فسخ خواهد بود:
            <br>
            عدم انجام وظایف محوله از سوی کارمند.
            <br>
            نقص قوانین و مقررات مطبعه.
            <br>
            انجام اعمال فساد و تقلب مانند مراعت نکردن معیارات اخلاقی، فعالیت های فسادکارانه (بیرون نمودن موضوعات داخلی
            و محرم مطبعه و امثال آن)، فعالیت های تقلبی (سوء استفاده از زمان کاری و هدر دادن وقت، جعل هر نوع اسناد و
            امثال آن)، سازش (همدستی و طرح میان دو یا بیشتر از دو شخص در صورت آگاهی یا عدم آگاهی اداره در انجام اعمال غیر
            اصولی).
            <br>
            غیر حاضری بیش سه روز بدون در جریان گذاشتن کارفرما و مدیریت.
            <br>
            انجام هر نوع عمل خلاف و غیر اخلاقی که در مغایرت با دین اسلام و قانون کشور قرار داشته باشد.
            <br>
            انجاد هر نوع عمل خلاف و بداخلاقی در مقابل ریس و مدیران مطبعه.
            <br>
            نوت: اسناد مورد ضرورت مانند: کاپی تذکره، فورم ضمانت خط و غیره باید ضم قرار داد باشد.
        </div>

    </body>

</html>
