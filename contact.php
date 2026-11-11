<?php
/* Contact form handler for one.com (PHP mail()).
   The pages post here with fetch() and get JSON back; without JS the visitor is
   redirected to the page they came from with ?envoi=ok|erreur#contact. */

const TO = 'info@tagada.be';
const FROM = 'info@tagada.be';            // must be an address on the domain (one.com rejects others)
const MAX_FILE_BYTES = 10 * 1024 * 1024;  // keep the e-mail deliverable; bigger files go through WeTransfer
const ALLOWED_EXT = ['pdf', 'ai', 'eps', 'dwg', 'stl', 'step', 'stp', 'obj', 'jpg', 'jpeg', 'png', 'zip'];
const MIN_SECONDS_BETWEEN_SENDS = 30;
const TYPE_OPTIONS_MAX = 80;

$wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

function finish(bool $ok, string $error = ''): void {
    global $wantsJson;
    if ($wantsJson) {
        http_response_code($ok ? 200 : 400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $ok, 'error' => $ok ? null : $error]);
        exit;
    }
    $back = $_SERVER['HTTP_REFERER'] ?? '/';
    $host = parse_url($back, PHP_URL_HOST);
    if ($host && $host !== ($_SERVER['HTTP_HOST'] ?? '')) $back = '/';
    $back = strtok($back, '?#');
    header('Location: ' . $back . '?envoi=' . ($ok ? 'ok' : 'erreur') . '#contact', true, 303);
    exit;
}

function field(string $name, int $max = 200): string {
    $v = trim((string)($_POST[$name] ?? ''));
    $v = str_replace(["\r", "\0"], '', $v);
    return mb_substr($v, 0, $max);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') finish(false, 'method');

// Honeypot: real visitors never see this field.
if (field('website') !== '') finish(true);

// Light per-IP rate limit.
$stamp = sys_get_temp_dir() . '/tagada_form_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
if (is_file($stamp) && time() - filemtime($stamp) < MIN_SECONDS_BETWEEN_SENDS) finish(false, 'rate');

$prenom  = field('prenom', 80);
$nom     = field('nom', 80);
$email   = field('email', 160);
$tel     = field('telephone', 40);
$type    = field('type', TYPE_OPTIONS_MAX);
$besoin  = field('besoin', 40);
$message = trim(str_replace("\0", '', (string)($_POST['message'] ?? '')));
$message = mb_substr($message, 0, 5000);
$lang    = in_array($_POST['lang'] ?? '', ['fr', 'en', 'nl'], true) ? $_POST['lang'] : 'fr';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) finish(false, 'email');
if (empty($_POST['consentement'])) finish(false, 'consent');
if ($message === '' && $prenom === '' && $nom === '') finish(false, 'empty');

// Optional attachment.
$attachment = null;
if (!empty($_FILES['fichier']) && $_FILES['fichier']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['fichier'];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > MAX_FILE_BYTES) finish(false, 'file_size');
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT, true) || !is_uploaded_file($f['tmp_name'])) finish(false, 'file_type');
    $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '_', basename($f['name']));
    $attachment = ['name' => $safeName, 'data' => file_get_contents($f['tmp_name'])];
}

$who = trim("$prenom $nom") ?: $email;
$subject = 'Demande via le site — ' . $who;
$body = "Nouvelle demande reçue via tagada.be\n\n"
      . "Nom : $prenom $nom\n"
      . "Email : $email\n"
      . "Téléphone : " . ($tel ?: '—') . "\n"
      . "Type de projet : " . ($type ?: '—') . "\n"
      . "Besoin : " . ($besoin ?: '—') . "\n"
      . "Langue du site : $lang\n"
      . "Fichier joint : " . ($attachment ? $attachment['name'] : '—') . "\n\n"
      . "Message :\n" . ($message ?: '—') . "\n";

$boundary = 'tagada_' . bin2hex(random_bytes(12));
$headers = [
    'From: Site Tagada <' . FROM . '>',
    'Reply-To: ' . (mb_encode_mimeheader($who, 'UTF-8') . ' <' . $email . '>'),
    'MIME-Version: 1.0',
];
if ($attachment) {
    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';
    $content = "--$boundary\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
             . chunk_split(base64_encode($body)) . "\r\n"
             . "--$boundary\r\n"
             . "Content-Type: application/octet-stream; name=\"{$attachment['name']}\"\r\n"
             . "Content-Transfer-Encoding: base64\r\n"
             . "Content-Disposition: attachment; filename=\"{$attachment['name']}\"\r\n\r\n"
             . chunk_split(base64_encode($attachment['data'])) . "\r\n"
             . "--$boundary--";
} else {
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'Content-Transfer-Encoding: base64';
    $content = chunk_split(base64_encode($body));
}

$sent = mail(TO, mb_encode_mimeheader($subject, 'UTF-8'), $content, implode("\r\n", $headers), '-f' . FROM);
if (!$sent) {
    error_log('tagada contact form: mail() failed for ' . $email);
    finish(false, 'send');
}
@touch($stamp);
finish(true);
