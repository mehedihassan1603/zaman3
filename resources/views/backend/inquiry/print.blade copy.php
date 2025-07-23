<!DOCTYPE html>
<html>
<head>
    <title>Inquiry Print</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header img {
            height: 80px;
        }
        .header h2 {
            margin: 10px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #000;
            text-align: left;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <img src="{{ asset('path/to/your-logo.png') }}" alt="Company Logo">
        <h2>Your Company Name</h2>
    </div>

    <h3>Reference Number: INQ-{{ str_pad($inquiry->id, 5, '0', STR_PAD_LEFT) }}</h3>

    <table>
        <tbody>
            @php $serial = 1; @endphp

            <tr>
                <td class="label">#</td>
                <td>{{ $serial++ }}</td>
            </tr>
            <tr>
                <td class="label">Date</td>
                <td>{{ $inquiry->date }}</td>
            </tr>
            <tr>
                <td class="label">Company Name</td>
                <td>{{ $inquiry->company_name }}</td>
            </tr>
            <tr>
                <td class="label">Contact Person</td>
                <td>{{ $inquiry->contact_person }}</td>
            </tr>
            <tr>
                <td class="label">Designation</td>
                <td>{{ $inquiry->designation }}</td>
            </tr>
            <tr>
                <td class="label">Contact Number</td>
                <td>{{ $inquiry->contact_number }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $inquiry->email }}</td>
            </tr>
            <tr>
                <td class="label">Head Office</td>
                <td>{{ $inquiry->head_office }}</td>
            </tr>
            <tr>
                <td class="label">Factory</td>
                <td>{{ $inquiry->factory }}</td>
            </tr>
            <tr>
                <td class="label">Requirement</td>
                <td>{{ $inquiry->requirement }}</td>
            </tr>
            <tr>
                <td class="label">Referred By</td>
                <td>{{ $inquiry->reffer }}</td>
            </tr>
            <tr>
                <td class="label">Remark</td>
                <td>{{ $inquiry->remark }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
