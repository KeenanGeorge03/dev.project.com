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

    $alerts = $dbh->prepare('SELECT r.username,r.email as "requestor",
							(SELECT email FROM authentication_tbl where id = 0) as "admin"
							FROM incident_tbl as i

							inner join
							authentication_tbl as r on r.id = i.requestor 

							where i.id = :id');
    $alerts->execute(array(":id" => $incident_id));
    $alert_email = $alerts->fetchAll();

    $cc = $dbh->prepare('SELECT cc_recipient from incident_detail_tbl
							where header_id = :id

							order by id desc
							Limit 0,1');
    $cc_recipients = $cc->execute(array(":id" => $incident_id));

    $status_name = $dbh->prepare('SELECT `status` from incident_status_tbl
							where id = :id');
    $status_name->execute(array(":id" => $data['status']));
    $status_name_retr = $status_name->fetchAll();

    require '../phpmailer/PHPMailerAutoload.php';

    $cc_reps = explode(';', $data['cc_recipient']);

	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'keenangrg@gmail.com';                 // SMTP username
	$mail->Password = 'Charnte#1';                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->setFrom('keenangrg@gmail.com', 'Keenan George Highsite Administrator');
	$mail->addAddress($alert_email[0]['email'], $alert_email[0]['username']);     // Add a recipient
	$mail->addReplyTo('no-reply@example.com', 'Information');
	
	for ($i=0; $i < count($cc_reps); $i++) { 
		$mail->addCC($cc_reps[$i]);
	}

	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = 'Do Not Reply: Incident #'.$incident_id.", subject: ".$data['subject'];
	$mail->Body    = 'Good day <br /> this is a notification email to let you know that incident #'.$incident_id.' has been updated.
						<br /><br />
						<strong>Status: </strong>'.($status_name_retr[0]['status'] != "" ? $status_name_retr[0]['status'] : 'New').'<hr />
						<h4>New Message:</h4>
						'.$data['body'].'<br /><hr /><br />
						For further details, visit: <a href="http://dev.project.com/pages/incident_manager.php?id='.$incident_id.'">'.$data['subject'].'</a>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	if(!$mail->send()) {
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
	    echo 'Message has been sent';
	}

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

function get_open_tickets($dbh){

	$open_tickets = $dbh->prepare('SELECT COUNT(*) as count
								   FROM incident_detail_tbl
								   WHERE status = 0');
	$open_tickets->execute();
	$results_open_tickets = $open_tickets->fetchAll();
	return $results_open_tickets;

}
function get_pending_tickets($dbh){

	$pending_tickets = $dbh->prepare('SELECT COUNT(*) as count
								   FROM incident_detail_tbl
								   WHERE status = 1');
	$pending_tickets->execute();
	$results_pending_tickets = $pending_tickets->fetchAll();
	return $results_pending_tickets;

}
function get_closed_tickets($dbh){

	$closed_tickets = $dbh->prepare('SELECT COUNT(*) as count
								   FROM incident_detail_tbl
								   WHERE status = 2');
	$closed_tickets->execute();
	$results_closed_tickets = $closed_tickets->fetchAll();
	return $results_closed_tickets;

}
?>


