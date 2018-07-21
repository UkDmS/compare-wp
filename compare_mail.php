<?
session_start();
$id = $_POST['attachment'] ;

if(isset($_SESSION[$id])) {
    $output = "<html><head><meta charset='UTF-8' /><script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script></head>";
    $output .= "<body>";
    $output .= $_SESSION[$id];
    $output .= "</body></html>";
    $path = "comparehtml/".$id.'.html';
    $fp = fopen($path, 'w');
    fwrite($fp, $output);
    fclose($fp);




    $mailto = $_POST['phone'];
	$from_mail="<admin@site.ru>";
    $from_name = "d";
    $subject = "сравнение";
    $message="g" ;



function XMail( $from, $to, $subj, $text, $filename) {
    $f         = fopen($filename,"rb");
    $un        = strtoupper(uniqid(time()));
    $head      = "From: $from\n";
    $head     .= "To: $to\n";
    $head     .= "Subject: $subj\n";
    $head     .= "X-Mailer: PHPMail Tool\n";
    $head     .= "Reply-To: $from\n";
    $head     .= "Mime-Version: 1.0\n";
    $head     .= "Content-Type:multipart/mixed;";
    $head     .= "boundary=\"----------".$un."\"\n\n";
    $zag       = "------------".$un."\nContent-Type:text/html;\n";
    $zag      .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
    $zag      .= "------------".$un."\n";
    $zag      .= "Content-Type: application/octet-stream;";
    $zag      .= "name=\"".basename($filename)."\"\n";
    $zag      .= "Content-Transfer-Encoding:base64\n";
    $zag      .= "Content-Disposition:attachment;";
    $zag      .= "filename=\"".basename($filename)."\"\n\n";
    $zag      .= chunk_split(base64_encode(fread($f,filesize($filename))))."\n";

    return @mail("$to", "$subj", $zag, $head);
}
    XMail($from_mail, $mailto, $subject, $message, $path);
    unlink ( $path );

}
?>
