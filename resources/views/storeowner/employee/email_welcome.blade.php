<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome - New Employee</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <p>Hello {{ $firstname }}&nbsp; {{ $lastname }},</p>
                    <br>
                    <br>
                    <p>Welcome aboard to {{ $storeName }},</p>
                    <br>
                    <p>Your Clock in-out code is: <strong>{{ $loginCode }}</strong></p>
                    <br>
                    <p>Please clock in when you are ready for work, and clock out when you finish your work.</p>
                    <br>
                    <p>Looking forward to working with you.</p>
                    <br>
                    <p>Best of luck.</p>
                    <br>
                    <p>{{ $storeName }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

