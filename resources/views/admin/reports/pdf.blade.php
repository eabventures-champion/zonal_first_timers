<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Weekly First Timers Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: center;
        }

        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .text-left {
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-size: 18px;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
            font-size: 12px;
        }

        .total-col {
            background-color: #f1f5f9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Weekly First Timers Report</h2>
        <p class="subtitle">For: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-left">Category</th>
                <th class="text-left">Group</th>
                <th class="text-left">Church</th>
                @foreach($weeksInMonth as $week)
                    <th>{{ strtoupper($week['start']) }}</th>
                @endforeach
                <th class="total-col">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $catName => $groups)
                @foreach($groups as $groupName => $churches)
                    @foreach($churches as $churchName => $stats)
                        <tr>
                            <td class="text-left">{{ $catName }}</td>
                            <td class="text-left">{{ $groupName }}</td>
                            <td class="text-left">{{ $churchName }}</td>
                            @foreach($stats['weeks'] as $weekCount)
                                <td>{{ $weekCount }}</td>
                            @endforeach
                            <td class="total-col">{{ $stats['total'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>