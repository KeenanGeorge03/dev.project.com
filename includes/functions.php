<?php

	// function to get all subjects from database.
	function get_all_identifications($dbh){
		$fetch_results = $dbh->prepare('SELECT
										 a.id,
										 a.rb_name,
										 a.rb_mac_address,
										 a.radio_name,
										 a.mode,
										 a.ssid,
										 a.frequency,
										 a.scan_list,
										 a.wireless_protocol,
										 a.date_time,
										 b.tx_ccq,
										 b.rx_ccq

										FROM rb_identity_tbl as a
										inner join
										rb_registration_tbl as b on b.rb_mac_address = a.rb_mac_address

										where a.rb_mac_address in(SELECT DISTINCT rb_mac_address from rb_identity_tbl)

										ORDER BY a.date_time desc
										LIMIT 0,1');
		
     	$fetch_results->execute();
     	$results = $fetch_results->fetchAll();
     	return $results;
	}

	function get_all_resources($mac,$dbh){
		$fetch_results = $dbh->prepare('SELECT

										 uptime,
										 version,
										 build_time,
										 cpu_mips,
										 cpu_load,
										 architecture_name,
										 board_name

										FROM rb_resources_tbl
										where rb_mac_address in(SELECT DISTINCT rb_mac_address from rb_resources_tbl) and rb_mac_address = :mac
										ORDER BY date_time desc
										LIMIT 0,1');		
     	$fetch_results->execute(array(":mac" => $mac));
     	$results = $fetch_results->fetchAll();
     	return $results;
	}

	function get_all_registration($mac,$dbh){
		$fetch_results = $dbh->prepare('SELECT

										 interface,
										 signal_strength,
										 signal_strength_ch0,
										 signal_strength_ch1,
										 rx_rate,
										 tx_rate,
										 tx_ccq,
										 rx_ccq

										FROM rb_registration_tbl
										where rb_mac_address in(SELECT DISTINCT rb_mac_address from rb_resources_tbl) and rb_mac_address = :mac
										ORDER BY date_time desc
										LIMIT 0,1');		
     	$fetch_results->execute(array(":mac" => $mac));
     	$results = $fetch_results->fetchAll();
     	return $results;
	}

	
function store_incident($data, $dbh){

	if ($_POST['new'] == 1) {

		$status = 0;

        $upload = $dbh->prepare('INSERT INTO 
        incident_tbl (requestor, subject, header_status, date_created) 
        VALUES (:requestor, :subject, :header_status, :date_created)');
        $results = $upload->execute(array(':requestor'=> $data['logged_in'],':subject' => $data['subject'],
        	':header_status' => 0,':date_created' => $data['date_created'])); 
        $incident_id = $dbh->lastInsertId();
	}
	else {
		$incident_id = $_POST['incident_id'];
		$status = $data['status'];

		$upload = $dbh->prepare('UPDATE 
        incident_tbl SET subject = :subject, header_status = :header_status where id = :id');

        $results = $upload->execute(array(':subject' => $data['subject'],
        	':header_status' => $data['status'],':id' => $incident_id)); 
	}

    $upload = $dbh->prepare('INSERT INTO 
    incident_detail_tbl (header_id, cc_recipient, body, date_created, status) 
    VALUES (:header_id, :cc_recipient, :body, :date_created, :status)');
    $results = $upload->execute(array(':header_id'=> $incident_id,':cc_recipient' => $data['cc_recipient'],
    	':body' => $data['body'],':date_created' => $data['date_created'],':status' => $status)); 
    // header("location:http://dev.project.com/pages/incident_create.php");

    $alerts = $dbh->prepare('SELECT r.email as "requestor",
							(SELECT email FROM authentication_tbl where id = 0) as "admin"
							FROM incident_tbl as i

							inner join
							authentication_tbl as r on r.id = i.requestor 

							where i.id = :id');
    $mail_recipients = $alerts->execute(array(":id" => $incident_id));

    $cc = $dbh->prepare('SELECT cc_recipient from incident_detail_tbl
							where header_id = :id

							order by id desc
							Limit 0,1');
    $cc_recipients = $cc->execute(array(":id" => $incident_id));

    // Please specify your Mail Server - Example: mail.example.com.
	ini_set("SMTP","ssl:smtp.gmail.com");

	// Please specify an SMTP Number 25 and 8889 are valid SMTP Ports.
	ini_set("smtp_port","465");

	// Please specify the return address to use
	ini_set('sendmail_from', 'keenangrg@gmail.com');

    $to = "keenangrg@gmail.com";
	$subject = "Incident #".$incident_id;

    $body = "
	<html>
	<head>
	<title>HTML email</title>
	</head>
	<body>
	<p>This email contains HTML Tags!</p>
	<table>
	<tr>
	<th>Firstname</th>
	<th>Lastname</th>
	</tr>
	<tr>
	<td>John</td>
	<td>Doe</td>
	</tr>
	</table>
	</body>
	</html>
	";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <keenangrg@gmail.com>' . "\r\n";
	// $headers .= 'Cc: myboss@example.com' . "\r\n";

    mail($to,$subject,$body,$headers);

    return $incident_id;
}

function get_count_incident($dbh)  {
									// $count = $dbh ->prepare('SELECT ')
}

function get_incident ($id,$dbh) {

	$header = $dbh->prepare('SELECT
								i.id, r.username, i.subject, i.date_created, s.`status`
								FROM incident_tbl as i
								inner join
								incident_status_tbl s on s.id = i.header_status
								inner join
								authentication_tbl r on r.id = i.requestor
								WHERE
								i.id = :id');		
 	$header->execute(array(":id" => $id));
 	$results = $header->fetchAll();

 	return $results;

}

function get_incident_history($id,$dbh)  {

	$detail = $dbh->prepare('SELECT
							i.id, i.header_id, i.cc_recipient, i.body, i.date_created, s.`status`
							FROM incident_detail_tbl as i
							inner join
							incident_status_tbl s on s.id = i.status
							WHERE
							i.header_id = :id');	

 	$detail->execute(array(":id" => $id));
 	$results = $detail->fetchAll();

 	return $results;
}

function get_statuses($dbh)  {

	$detail = $dbh->prepare('SELECT
							id,status
							FROM incident_status_tbl');	

 	$detail->execute();
 	$results = $detail->fetchAll();

 	return $results;
}


?>


