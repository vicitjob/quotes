<!DOCTYPE html>
<html>
<head>
    <title>Growel - Gate Entry</title>
    <style type="text/css">
        table {
          border-collapse: collapse;
          width: 100%;
        }

        td, th {
          
          text-align: left;
          padding: 5px;
          vertical-align:middle;
          border: 1px solid #000;
        }
    </style>
</head>
<body>
    
    <p>Dear {{ $mailData['user_name'] }},</p>
    <p>{{ $mailData['body'] }}</p>

    <table>
        @if($mailData['gate_in_no'] != '')
        <tr>
            <td style="font-weight: bold;">Gate Ref. No.</td>
            <td>{{ $mailData['gate_in_no'] }}</td>
			<td style="font-weight: bold;">Gate Name</td>
            <td>{{ $mailData['sec_id_gt_in_name'] }}</td>
        </tr>
        @endif
        <tr>
            <td style="font-weight: bold;">Gate In Date</td>
            <td>{{ $mailData['gate_in_time'] }}</td>
			<td style="font-weight: bold;">Gate Out Date</td>
            <td>{{ $mailData['gate_out_time'] }}</td>
        </tr>
        
        <tr>
            <td style="font-weight: bold;">Doc. Type</td>
            <td>{{ $mailData['doc_type_name'] }}</td>
            <td style="font-weight: bold;">Doc. No.</td>
            <td>{{ $mailData['doc_no'] }}</td>
        </tr>
		
		<tr>
            <td style="font-weight: bold;">Vehicle Type</td>
            <td>{{ $mailData['vehicle_type_desc'] }}</td>
            <td style="font-weight: bold;">Vehicle No.</td>
            <td>{{ $mailData['vehicle_no'] }}</td>
        </tr>
		
		<tr>
            <td style="font-weight: bold;">Department</td>
            <td>{{ $mailData['dept_name'] }}</td>
            <td style="font-weight: bold;">Vendor Name</td>
            <td>{{ $mailData['vendorname'] }}</td>
        </tr>
        
    </table>
	<p>Please find attachment.</p>
    <p><a href="{{ $mailData['form_url'] }}">Click Here For More Details</a></p>
            
    <p>Regards,</p>
    <p>{{ $mailData['sender'] }}</p>
</body>
</html>