<style>
	function comment($hash)
		{
			include("include/settings.php");
		}
	$hash= $_GET["data_hash"];
	if($hash)
		{
		comment($hash);
		}
?>