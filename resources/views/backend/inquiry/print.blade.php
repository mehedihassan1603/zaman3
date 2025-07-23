<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proposal Letter</title>
    <style>

        /*first page start*/
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            width: 150px;
        }

        .company-info {
            text-align: right;
        }

        .company-info h2 {
            margin: 0;
            color: #000066;
        }

        .content {
            margin-top: 20px;
        }

        .subject {
            font-weight: bold;
            margin: 20px 0;
            /*text-decoration: underline;*/
        }

        .signature {
            margin-top: 50px;
        }

        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #555;
        }

        .contact-info {
            margin-top: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .top-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }

        .top-info .left {
            text-align: left;
        }

        .top-info .right {
            position: relative;
            left: -15px;
        }
        .letter-body {
            text-align: justify;
            margin-top: 20px;
            line-height: 1.8;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            font-size: 14px;
            color: #000;
        }
        .footer-left {
            text-align: left;
        }

        .footer-right {
            text-align: right;
        }



        /*second page start*/
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .header, .footer {
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #000066;
        }
        .header p {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        .terms {
            margin-top: 20px;
        }
        .signature {
            margin-top: 50px;
        }
        .signature p {
            margin: 2px 0;
        }
        .inword {
            font-weight: bold;
            margin-top: 10px;
        }


    </style>
</head>
<body style="margin:auto;">

<div class="container header" style="border-bottom: 4px solid yellow; padding-bottom: 10px;" >
    <div>
        <img src="{{ asset('images/logo/zml_logo.PNG') }}" alt="ZML Logo" class="">
    </div>
    <div class="company-info imgLogo" style="position: relative;top: 12px;">
        <div>
            <img src="{{ asset('images/logo/zml.PNG') }}" alt="ZML Logo" class="">
        </div>
        <p style="font-size: 24px;margin-top: -5px;margin-bottom: 0;position: relative;left: -10px;">House# 16, Road-12, Block-C, Section-12, Pallabi, Dhaka-1216</p>
    </div>
</div>

<div class="content" style="margin-top: 20px;margin-left: 14px;font-size: 16px;">
    <div class="top-info">
        <p class="left" id="todayDate"></p>
        <p class="right">Ref: ZML/25051022<br>Ship To: {{ $inquiry->head_office }}</p>
    </div>
    <p>
        To<br>
        Managing Director<br>
        <span class="bold">{{ $inquiry->company->name }}</span><br>
        {{ $inquiry->head_office }}<br><br>
        <span class="bold">Attention:</span><br>
        {{ $inquiry->contact_person }}<br>
        {{ $inquiry->designation }}<br>
        Phone: {{ $inquiry->contact_number }}<br>
        E-Mail: <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
    </p>


    @php
        $productNames = $products->pluck('name')->toArray();
        $count = count($productNames);
    @endphp

    <p>
        <strong>Subject:</strong> Proposal for supply of
        <span class="subject">
            @if($count === 1)
                {{ $productNames[0] }}
            @elseif($count === 2)
                {{ $productNames[0] }} and {{ $productNames[1] }}
            @elseif($count > 2)
                {{ implode(', ', array_slice($productNames, 0, -1)) }} and {{ end($productNames) }}
            @endif
            </span>
    </p>


    <div class="letter-body">
        <p>
            Dear Sir,<br>
            Assalamu Alaikum and good day. Hope you are doing well and in good health.
        </p>

        <p>
            We take immense pleasure and honor for giving us opportunity to work for your company. Zaman Machineries has been working with several industries providing turnkey solutions for Industrial Chillers, Cooling Towers, Heat Exchangers, VIK AsIA, Hai Industrial centrifugal Pump and HVAC Systems. We will be pleased to share our success stories with you in our company profile. In this document we have provided you with the price offer for supply of
            <span class="bold">
                    @if($count === 1)
                    {{ $productNames[0] }}
                @elseif($count === 2)
                    {{ $productNames[0] }} and {{ $productNames[1] }}
                @elseif($count > 2)
                    {{ implode(', ', array_slice($productNames, 0, -1)) }} and {{ end($productNames) }}
                @endif
                </span> along with installation, testing and commissioning.
        </p>

        <p>
            For better clarification of supplies and prices, we have tried our best to provide you with the least price while ensuring top quality products.
        </p>

        <p>
            For any further clarifications please feel free to contact us.
        </p>
    </div>


    <div class="signature">
        <p>Sincerely,<br><br>
            Md. Moinuzzaman<br>
            Managing Director<br>
            Zaman Machineries Ltd.<br>
            Email: <a href="mailto:md.zaman@zamanmachineries.com">md.zaman@zamanmachineries.com</a><br>
            Cell: +880-17-27803003<br>
            <a href="https://www.zamanmachineries.com">www.zamanmachineries.com</a>
        </p>
    </div>

    <div class="footer">
        <div class="footer-left">
            <span style="font-weight: bold;">Email:</span> <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a>
        </div>
        <div class="footer-right">
            <span style="font-weight: bold;">Phone: 01782-579063</span>
        </div>
    </div>

</div>




{{-- 2nd page--}}

<div class="container header" style="margin-top: 150px; border-bottom: 4px solid yellow; padding-bottom: 10px;">
    <div>
        <img src="{{ asset('images/logo/zml_logo.PNG') }}" alt="ZML Logo" class="">
    </div>
    <div class="company-info imgLogo" style="position: relative; top: 12px;">
        <div>
            <img src="{{ asset('images/logo/zml.PNG') }}" alt="ZML Logo" class="">
        </div>
        <p style="font-size: 24px; margin-top: -5px; margin-bottom: 0; position: relative; left: -10px;">
            House# 16, Road-12, Block-C, Section-12, Pallabi, Dhaka-1216
        </p>
    </div>
</div>


<div style="text-align: center;">
    <h2 style="border-bottom: 2px solid black; display: inline-block;">Quotation</h2>
</div>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Sl No</th>
        <th>Product Description</th>
        <th>Unit</th>
        <th>Qty</th>
        <th>Unit Price (BDT)</th>
        <th>Amount (BDT)</th>
    </tr>
    </thead>

    <tbody>
    @php $total = 0; @endphp
    @foreach($products as $index => $product)
        @php
            $qty = 1;
            $amount = $product->price * $qty;
            $total += $amount;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>
                <strong>{{ $product->name }}</strong><br>
                {!! $product->product_details !!}
            </td>
            <td>{{ $product->unit->unit_name ?? 'pcs' }}</td>
            <td>{{ $qty }}</td>
            <td>{{ number_format($product->price, 2) }}</td>
            <td>{{ number_format($amount, 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5" style="text-align: right;"><strong>Total =</strong></td>
        <td><strong>{{ number_format($total, 2) }} Tk</strong></td>
    </tr>
    </tbody>
</table>

@php
    $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
    $totalInWords = ucwords($f->format($total));
@endphp


<div class="inword" style="border: 1px solid #000;padding: 8px;margin-top: -11px;font-weight: bold;text-align: center;">
    In Word: {{ $totalInWords }} Taka Only.
</div>


<div class="terms">
    <strong>Terms & Conditions:</strong>
    <ul style="list-style: none; padding-left: 20px;">
        <li><span style="margin-right: 6px;">&#10148;</span>This price including transportation excluding installation.</li>
        <li><span style="margin-right: 6px;">&#10148;</span>Excluding VAT & AIT</li>
        <li><span style="margin-right: 6px;">&#10148;</span>Delivery: 7 days from PO</li>
        <li><span style="margin-right: 6px;">&#10148;</span>Payment: 100% advance with PO.</li>
        <li><span style="margin-right: 6px;">&#10148;</span>Validity: 15 days from quoted dates</li>
        <li><span style="margin-right: 6px;">&#10148;</span>Warranty: N/A.</li>
    </ul>
</div>


<div class="signature" style="padding-left: 50px;">
    <p class="text-muted">Sincerely,</p>
    <br><br>
    <p><strong>Md. Moniruzzaman</strong><br>
        MBA in Marketing (DU)<br>
        Managing Director<br>
        <strong>Zaman Machineries Ltd.</strong><br>
        Email: <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a><br>
        Cell: +8801782-579033</p>
</div>

<div class="footer">
    <div class="footer-left">
        <span style="font-weight: bold;">Email:</span> <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a>
    </div>
    <div class="footer-right">
        <span style="font-weight: bold;">Phone: 01782-579063</span>
    </div>
</div>


<script>
    // Date formatting function
    function formatDate(date) {
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('en-GB', options); // Format: 06 May 2025
    }
    document.getElementById('todayDate').textContent = formatDate(new Date());
    // end method
</script>
</body>
</html>


























{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <title>Proposal Letter</title>--}}
{{--    <style>--}}

{{--        /*first page start*/--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            font-size: 14px;--}}
{{--            line-height: 1.6;--}}
{{--            color: #000;--}}
{{--            padding: 40px;--}}
{{--        }--}}

{{--        .header {--}}
{{--            display: flex;--}}
{{--            justify-content: space-between;--}}
{{--            align-items: center;--}}
{{--        }--}}

{{--        .logo {--}}
{{--            width: 150px;--}}
{{--        }--}}

{{--        .company-info {--}}
{{--            text-align: right;--}}
{{--        }--}}

{{--        .company-info h2 {--}}
{{--            margin: 0;--}}
{{--            color: #000066;--}}
{{--        }--}}

{{--        .content {--}}
{{--            margin-top: 20px;--}}
{{--        }--}}

{{--        .subject {--}}
{{--            font-weight: bold;--}}
{{--            margin: 20px 0;--}}
{{--            /*text-decoration: underline;*/--}}
{{--        }--}}

{{--        .signature {--}}
{{--            margin-top: 50px;--}}
{{--        }--}}

{{--        .footer {--}}
{{--            margin-top: 30px;--}}
{{--            font-size: 13px;--}}
{{--            color: #555;--}}
{{--        }--}}

{{--        .contact-info {--}}
{{--            margin-top: 10px;--}}
{{--        }--}}

{{--        .bold {--}}
{{--            font-weight: bold;--}}
{{--        }--}}

{{--        .underline {--}}
{{--            text-decoration: underline;--}}
{{--        }--}}

{{--        .top-info {--}}
{{--            display: flex;--}}
{{--            justify-content: space-between;--}}
{{--            align-items: flex-start;--}}
{{--            margin-top: 20px;--}}
{{--        }--}}

{{--        .top-info .left {--}}
{{--            text-align: left;--}}
{{--        }--}}

{{--        .top-info .right {--}}
{{--            position: relative;--}}
{{--            left: -15px;--}}
{{--        }--}}
{{--        .letter-body {--}}
{{--            text-align: justify;--}}
{{--            margin-top: 20px;--}}
{{--            line-height: 1.8;--}}
{{--        }--}}
{{--        .footer {--}}
{{--            display: flex;--}}
{{--            justify-content: space-between;--}}
{{--            margin-top: 30px;--}}
{{--            font-size: 14px;--}}
{{--            color: #000;--}}
{{--        }--}}
{{--        .footer-left {--}}
{{--            text-align: left;--}}
{{--        }--}}

{{--        .footer-right {--}}
{{--            text-align: right;--}}
{{--        }--}}



{{--        /*second page start*/--}}
{{--        body {--}}
{{--            font-family: Arial, sans-serif;--}}
{{--            font-size: 14px;--}}
{{--            margin: 20px;--}}
{{--        }--}}
{{--        .header, .footer {--}}
{{--            text-align: center;--}}
{{--        }--}}
{{--        .header h1 {--}}
{{--            margin: 0;--}}
{{--            color: #000066;--}}
{{--        }--}}
{{--        .header p {--}}
{{--            margin: 0;--}}
{{--        }--}}
{{--        table {--}}
{{--            width: 100%;--}}
{{--            border-collapse: collapse;--}}
{{--            margin-top: 20px;--}}
{{--            margin-bottom: 10px;--}}
{{--        }--}}
{{--        table, th, td {--}}
{{--            border: 1px solid #000;--}}
{{--        }--}}
{{--        th, td {--}}
{{--            padding: 6px;--}}
{{--            text-align: left;--}}
{{--        }--}}
{{--        .terms {--}}
{{--            margin-top: 20px;--}}
{{--        }--}}
{{--        .signature {--}}
{{--            margin-top: 50px;--}}
{{--        }--}}
{{--        .signature p {--}}
{{--            margin: 2px 0;--}}
{{--        }--}}
{{--        .inword {--}}
{{--            font-weight: bold;--}}
{{--            margin-top: 10px;--}}
{{--        }--}}


{{--    </style>--}}
{{--</head>--}}
{{--<body style="margin:auto;">--}}

{{--    <div class="container header" style="border-bottom: 4px solid yellow; padding-bottom: 10px;" >--}}
{{--        <div>--}}
{{--            <img src="{{ asset('images/logo/zml_logo.PNG') }}" alt="ZML Logo" class="">--}}
{{--        </div>--}}
{{--        <div class="company-info imgLogo" style="position: relative;top: 12px;">--}}
{{--            <div>--}}
{{--                <img src="{{ asset('images/logo/zml.PNG') }}" alt="ZML Logo" class="">--}}
{{--            </div>--}}
{{--            <p style="font-size: 24px;margin-top: -5px;margin-bottom: 0;position: relative;left: -10px;">House# 16, Road-12, Block-C, Section-12, Pallabi, Dhaka-1216</p>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="content" style="margin-top: 20px;margin-left: 14px;font-size: 16px;">--}}
{{--        <div class="top-info">--}}
{{--            <p class="left" id="todayDate"></p>--}}
{{--            <p class="right">Ref: ZML/25051022<br>Ship To: {{ $inquiry->head_office }}</p>--}}
{{--        </div>--}}
{{--        <p>--}}
{{--        To<br>--}}
{{--            Managing Director<br>--}}
{{--            <span class="bold">{{ $inquiry->company->name }}</span><br>--}}
{{--                {{ $inquiry->head_office }}<br><br>--}}
{{--            <span class="bold">Attention:</span><br>--}}
{{--            {{ $inquiry->contact_person }}<br>--}}
{{--        {{ $inquiry->designation }}<br>--}}
{{--            Phone: {{ $inquiry->contact_number }}<br>--}}
{{--            E-Mail: <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>--}}
{{--        </p>--}}


{{--        @php--}}
{{--            $productNames = $products->pluck('name')->toArray();--}}
{{--            $count = count($productNames);--}}
{{--        @endphp--}}

{{--        <p>--}}
{{--            <strong>Subject:</strong> Proposal for supply of--}}
{{--            <span class="subject">--}}
{{--            @if($count === 1)--}}
{{--                {{ $productNames[0] }}--}}
{{--            @elseif($count === 2)--}}
{{--                {{ $productNames[0] }} and {{ $productNames[1] }}--}}
{{--            @elseif($count > 2)--}}
{{--                {{ implode(', ', array_slice($productNames, 0, -1)) }} and {{ end($productNames) }}--}}
{{--            @endif--}}
{{--            </span>--}}
{{--        </p>--}}


{{--        <div class="letter-body">--}}
{{--            <p>--}}
{{--                Dear Sir,<br>--}}
{{--                Assalamu Alaikum and good day. Hope you are doing well and in good health.--}}
{{--            </p>--}}

{{--            <p>--}}
{{--                We take immense pleasure and honor for giving us opportunity to work for your company. Zaman Machineries has been working with several industries providing turnkey solutions for Industrial Chillers, Cooling Towers, Heat Exchangers, VIK AsIA, Hai Industrial centrifugal Pump and HVAC Systems. We will be pleased to share our success stories with you in our company profile. In this document we have provided you with the price offer for supply of--}}
{{--                <span class="bold">--}}
{{--                    @if($count === 1)--}}
{{--                        {{ $productNames[0] }}--}}
{{--                    @elseif($count === 2)--}}
{{--                        {{ $productNames[0] }} and {{ $productNames[1] }}--}}
{{--                    @elseif($count > 2)--}}
{{--                        {{ implode(', ', array_slice($productNames, 0, -1)) }} and {{ end($productNames) }}--}}
{{--                    @endif--}}
{{--                </span> along with installation, testing and commissioning.--}}
{{--            </p>--}}

{{--            <p>--}}
{{--                For better clarification of supplies and prices, we have tried our best to provide you with the least price while ensuring top quality products.--}}
{{--            </p>--}}

{{--            <p>--}}
{{--                For any further clarifications please feel free to contact us.--}}
{{--            </p>--}}
{{--        </div>--}}


{{--        <div class="signature">--}}
{{--            <p>Sincerely,<br><br>--}}
{{--                Md. Moinuzzaman<br>--}}
{{--                Managing Director<br>--}}
{{--                Zaman Machineries Ltd.<br>--}}
{{--                Email: <a href="mailto:md.zaman@zamanmachineries.com">md.zaman@zamanmachineries.com</a><br>--}}
{{--                Cell: +880-17-27803003<br>--}}
{{--                <a href="https://www.zamanmachineries.com">www.zamanmachineries.com</a>--}}
{{--            </p>--}}
{{--        </div>--}}

{{--        <div class="footer">--}}
{{--            <div class="footer-left">--}}
{{--                <span style="font-weight: bold;">Email:</span> <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a>--}}
{{--            </div>--}}
{{--            <div class="footer-right">--}}
{{--                <span style="font-weight: bold;">Phone: 01782-579063</span>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--    </div>--}}




{{-- 2nd page--}}

{{--    <div class="container header" style="margin-top: 150px; border-bottom: 4px solid yellow; padding-bottom: 10px;">--}}
{{--        <div>--}}
{{--            <img src="{{ asset('images/logo/zml_logo.PNG') }}" alt="ZML Logo" class="">--}}
{{--        </div>--}}
{{--        <div class="company-info imgLogo" style="position: relative; top: 12px;">--}}
{{--            <div>--}}
{{--                <img src="{{ asset('images/logo/zml.PNG') }}" alt="ZML Logo" class="">--}}
{{--            </div>--}}
{{--            <p style="font-size: 24px; margin-top: -5px; margin-bottom: 0; position: relative; left: -10px;">--}}
{{--                House# 16, Road-12, Block-C, Section-12, Pallabi, Dhaka-1216--}}
{{--            </p>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--    <div style="text-align: center;">--}}
{{--        <h2 style="border-bottom: 2px solid black; display: inline-block;">Quotation</h2>--}}
{{--    </div>--}}

{{--    <table border="1" cellpadding="8" cellspacing="0" width="100%">--}}
{{--        <thead>--}}
{{--        <tr>--}}
{{--            <th>Sl No</th>--}}
{{--            <th>Product Description</th>--}}
{{--            <th>Unit</th>--}}
{{--            <th>Qty</th>--}}
{{--            <th>Unit Price (BDT)</th>--}}
{{--            <th>Amount (BDT)</th>--}}
{{--        </tr>--}}
{{--        </thead>--}}
{{--        <tbody>--}}
{{--        @php $total = 0; @endphp--}}
{{--        @foreach($products as $index => $product)--}}
{{--            @php--}}
{{--                $qty = 1;--}}
{{--                $amount = $product->price * $qty;--}}
{{--                $total += $amount;--}}
{{--            @endphp--}}
{{--            <tr>--}}
{{--                <td>{{ $index + 1 }}</td>--}}
{{--                <td>--}}
{{--                    <strong>{{ $product->name }}</strong><br>--}}
{{--                    {{ $product->description }}--}}
{{--                </td>--}}
{{--                <td>{{ $product->unit->unit_name ?? 'pcs' }}</td>--}}
{{--                <td>{{ $qty }}</td>--}}
{{--                <td>{{ number_format($product->price, 2) }}</td>--}}
{{--                <td>{{ number_format($amount, 2) }}</td>--}}
{{--            </tr>--}}
{{--        @endforeach--}}
{{--        <tr>--}}
{{--            <td colspan="5" style="text-align: right;"><strong>Total =</strong></td>--}}
{{--            <td><strong>{{ number_format($total, 2) }} Tk</strong></td>--}}
{{--        </tr>--}}
{{--        </tbody>--}}
{{--    </table>--}}

{{--    @php--}}
{{--        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);--}}
{{--        $totalInWords = ucwords($f->format($total));  --}}
{{--    @endphp--}}


{{--    <div class="inword" style="border: 1px solid #000;padding: 8px;margin-top: -11px;font-weight: bold;text-align: center;">--}}
{{--        In Word: {{ $totalInWords }} Taka Only.--}}
{{--    </div>--}}


{{--    <div class="terms">--}}
{{--        <strong>Terms & Conditions:</strong>--}}
{{--        <ul style="list-style: none; padding-left: 20px;">--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>This price including transportation excluding installation.</li>--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>Excluding VAT & AIT</li>--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>Delivery: 7 days from PO</li>--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>Payment: 100% advance with PO.</li>--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>Validity: 15 days from quoted dates</li>--}}
{{--            <li><span style="margin-right: 6px;">&#10148;</span>Warranty: N/A.</li>--}}
{{--        </ul>--}}
{{--    </div>--}}


{{--    <div class="signature" style="padding-left: 50px;">--}}
{{--        <p class="text-muted">Sincerely,</p>--}}
{{--        <br><br>--}}
{{--        <p><strong>Md. Moniruzzaman</strong><br>--}}
{{--            MBA in Marketing (DU)<br>--}}
{{--            Managing Director<br>--}}
{{--            <strong>Zaman Machineries Ltd.</strong><br>--}}
{{--            Email: <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a><br>--}}
{{--            Cell: +8801782-579033</p>--}}
{{--    </div>--}}

{{--    <div class="footer">--}}
{{--        <div class="footer-left">--}}
{{--            <span style="font-weight: bold;">Email:</span> <a href="mailto:sales@zamanmachineries.com">sales@zamanmachineries.com</a>--}}
{{--        </div>--}}
{{--        <div class="footer-right">--}}
{{--            <span style="font-weight: bold;">Phone: 01782-579063</span>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--    <script>--}}
{{--        // Date formatting function--}}
{{--        function formatDate(date) {--}}
{{--            const options = { day: '2-digit', month: 'long', year: 'numeric' };--}}
{{--            return date.toLocaleDateString('en-GB', options); // Format: 06 May 2025--}}
{{--        }--}}
{{--        document.getElementById('todayDate').textContent = formatDate(new Date());--}}
{{--        // end method--}}
{{--    </script>--}}
{{--</body>--}}
{{--</html>--}}


