<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: kontakt.html");
  exit;
}

$namn = trim($_POST["namn"] ?? "");
$alder = trim($_POST["alder"] ?? "");
$epost = trim($_POST["epost"] ?? "");
$meddelande = trim($_POST["meddelande"] ?? "");

if ($namn === "" || $alder === "" || $epost === "" || $meddelande === "") {
  die("Alla fält måste fyllas i.");
}

if (!filter_var($epost, FILTER_VALIDATE_EMAIL)) {
  die("Ogiltig e-postadress.");
}

$to = "yamoo1010@gmail.com";
$subject = "Kontaktformulär – Noda UF";

$body =
"Namn: $namn\n" .
"Ålder: $alder\n" .
"E-post: $epost\n\n" .
"Meddelande:\n$meddelande\n";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: Noda UF <no-reply@nodauf.se>\r\n";
$headers .= "Reply-To: $epost\r\n";

$ok = mail($to, $subject, $body, $headers);

if ($ok) {
  header("Location: tack.html");
  exit;
} else {
  die("Kunde inte skicka just nu. Försök igen senare.");
}
