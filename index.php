<?php

$dbuser = 'root';
$dbserv = 'localhost';
$dbname = 'shorten_amirsayonara';
$dbpass = '';
$conn = @mysqli_connect($dbserv, $dbuser, $dbpass) or die('Koneksi gagal');
@mysqli_select_db($conn, $dbname) or die('Database tidak ada');

function random($length) {
	$randomBytes = openssl_random_pseudo_bytes($length);
	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$charactersLength = strlen($characters);
	$result = '';
	for ($i = 0; $i < $length; $i++)
		$result .= $characters[ord($randomBytes[$i]) % $charactersLength];
	return $result;
}

function ambil_url($url) {
	global $conn;
	$sql = mysqli_query($conn, "select * from url where id='$url'");
	$hasil= mysqli_fetch_array($sql);
		if ($hasil > 0)
			return $hasil['url'];
		else return null;
}

function pasang_url($id, $url) {
	global $conn;
	$sql = mysqli_query($conn, "insert into url (id, url) values ('$id', '$url')");
}

function url_check($url) {
	if (strpos($url, 'http://') !== false | strpos($url, 'https://')!== false)
    	return $url;
	else return 'http://'.$url;
}

@$hal = $_GET['hal'];
if ($hal!=null) {
	$direct = ambil_url(str_replace('/', '', $_SERVER['REQUEST_URI']));
	if ($direct==null) {
		echo "URL tidak ada"; exit();
	} else header('Location:'.url_check($direct));
}
if (isset($_POST['url']) & isset($_POST['custom'])) {
	if ($_COOKIE['sudah']==1) {
		header('Location: .');
	} else {
		$url = $_POST['url'];
		$custom = $_POST['custom'];
		if ($custom=="") {
			$hasil = random(5);
			while (ambil_url($hasil)!=null) {
				$hasil = random(5);
			}
		} else if (ambil_url($custom)==null) {
			$hasil = $custom;
		} else {
			$hasil = 'URL sudah ada</td><td><button type="button" onclick="history.back();">Kembali</button>';
		}

		if ($hasil!= 'URL sudah ada</td><td><button type="button" onclick="history.back();">Kembali</button>' & ambil_url($hasil)==null) {
			pasang_url($hasil, $url);
			//$hasil = 'http://'.$_SERVER['SERVER_NAME'].'/'.$hasil.'</td><td><button type="button" onclick="location.reload();">Halaman Awal</button>';
			$hasil = 'http://s.yr/'.$hasil.'</td><td><button type="button" onclick="location.reload();">Halaman Awal</button>';
		} else {
			$hasil = 'URL sudah ada</td><td><button type="button" onclick="history.back();">Kembali</button>';
		}

		$halaman = '<table>
			<tr>
				<td>Hasil:</td><td>'.$hasil.'</td>
			</tr>
		</table>
';
		setcookie('sudah', 1);
	}
} else {
	$halaman = '<form method="post">
			<table>
				<tr>
					<td>URL panjang anda:</td>
					<td><input type="url" name="url" required></td>
				</tr><tr>
					<td>Tentukan URL anda sendiri (tidak wajib):</td>
					<td><input type="text" name="custom"></td>
				</tr><tr>
					<td colspan="2"><input type="submit" value="Kirim"></td>
				</tr>
			</table>
		</form>
';
	setcookie('sudah', 0);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Amir Sayonara URL Shortener</title>
	</head>
	<body>
		<?=$halaman?>
	</body>
</html>